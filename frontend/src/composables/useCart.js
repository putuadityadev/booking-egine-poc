import { ref, computed, watch } from 'vue'
import { useAuth } from './useAuth'

const CART_STORAGE_KEY = 'stayhub_cart_items_v2'

// Persisted cart state
const cartItems = ref(loadInitialCart())
const isCartOpen = ref(false)
const isCheckoutModalOpen = ref(false)

function loadInitialCart() {
  try {
    const raw = localStorage.getItem(CART_STORAGE_KEY)
    return raw ? JSON.parse(raw) : []
  } catch (err) {
    return []
  }
}

// Watch and sync with local storage
watch(
  cartItems,
  (items) => {
    try {
      localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(items))
    } catch (err) {
      console.warn('Failed to persist cart items to localStorage', err)
    }
  },
  { deep: true }
)

export function useCart() {
  /**
   * Add a room to the booking cart.
   * If the exact same room and dates already exist, increment quantity.
   */
  const addToCart = (room, property, options = {}) => {
    const checkIn = options.checkIn || '2026-09-15'
    const checkOut = options.checkOut || '2026-09-17'
    const nights = Math.max(1, options.nights || 2)
    const guests = Math.max(1, options.guests || 2)
    const memberTier = options.memberTier || null
    const isMember = Boolean(options.isMember)

    // Calculate member discount rate
    let discountPercent = 0
    let discountAmountPerNight = 0

    if (isMember && room.is_member_rate_applicable && memberTier) {
      const rates = room.tier_discount_rates || {
        Bronze: 5,
        Silver: 10,
        Gold: 15,
        Diamond: 20,
      }
      const normalizedTier = memberTier.charAt(0).toUpperCase() + memberTier.slice(1).toLowerCase()
      discountPercent = rates[normalizedTier] || rates[memberTier] || 10
      discountAmountPerNight = Math.round((room.base_price * discountPercent) / 100)
    }

    const nightlyPrice = Math.max(0, room.base_price - discountAmountPerNight)

    // Check if duplicate item exists
    const existingIndex = cartItems.value.findIndex(
      (item) => item.roomId === room.id && item.checkIn === checkIn && item.checkOut === checkOut
    )

    if (existingIndex > -1) {
      cartItems.value[existingIndex].quantity += 1
    } else {
      const newItem = {
        id: `cart-${room.id}-${Date.now()}`,
        roomId: room.id,
        roomCode: room.code,
        roomName: room.name,
        roomImage: room.image_url,
        propertyId: property?.id || 1,
        propertyName: property?.name || 'StayHub Luxury Hotel',
        checkIn,
        checkOut,
        nights,
        guests,
        quantity: 1,
        basePrice: Number(room.base_price),
        discountPercent,
        discountAmountPerNight,
        nightlyPrice,
        isMemberRate: discountPercent > 0,
        appliedTier: discountPercent > 0 ? memberTier : null,
      }
      cartItems.value.push(newItem)
    }

    // Automatically reveal bottom-right cart drawer
    isCartOpen.value = true
  }

  const removeFromCart = (itemId) => {
    cartItems.value = cartItems.value.filter((item) => item.id !== itemId)
    if (cartItems.value.length === 0) {
      isCartOpen.value = false
    }
  }

  const updateQuantity = (itemId, quantity) => {
    const item = cartItems.value.find((i) => i.id === itemId)
    if (!item) return

    if (quantity <= 0) {
      removeFromCart(itemId)
    } else {
      item.quantity = quantity
    }
  }

  const updateNights = (itemId, nights) => {
    const item = cartItems.value.find((i) => i.id === itemId)
    if (!item) return
    const newNights = Math.max(1, nights)
    item.nights = newNights

    // Recalculate checkOut so backend date diff stays in sync with nights stepper
    const checkInDate = new Date(item.checkIn + 'T00:00:00')
    checkInDate.setDate(checkInDate.getDate() + newNights)
    const yyyy = checkInDate.getFullYear()
    const mm = String(checkInDate.getMonth() + 1).padStart(2, '0')
    const dd = String(checkInDate.getDate()).padStart(2, '0')
    item.checkOut = `${yyyy}-${mm}-${dd}`
  }

  const updateGuests = (itemId, guests) => {
    const item = cartItems.value.find((i) => i.id === itemId)
    if (!item) return
    item.guests = Math.max(1, guests)
  }

  const clearCart = () => {
    cartItems.value = []
    isCartOpen.value = false
  }

  const toggleCart = () => {
    isCartOpen.value = !isCartOpen.value
  }

  const openCart = () => {
    isCartOpen.value = true
  }

  const closeCart = () => {
    isCartOpen.value = false
  }

  const openCheckout = () => {
    if (cartItems.value.length === 0) return
    isCartOpen.value = false
    isCheckoutModalOpen.value = true
  }

  const closeCheckout = () => {
    isCheckoutModalOpen.value = false
  }

  // Recalculate member discounts when member status changes
  const refreshCartMemberRates = (isMember, memberTier) => {
    cartItems.value.forEach((item) => {
      let discountPercent = 0
      let discountAmountPerNight = 0

      if (isMember && memberTier) {
        const rates = {
          Bronze: 5,
          Silver: 10,
          Gold: 15,
          Diamond: 20,
        }
        const normalizedTier = memberTier.charAt(0).toUpperCase() + memberTier.slice(1).toLowerCase()
        discountPercent = rates[normalizedTier] || rates[memberTier] || 10
        discountAmountPerNight = Math.round((item.basePrice * discountPercent) / 100)
      }

      item.discountPercent = discountPercent
      item.discountAmountPerNight = discountAmountPerNight
      item.nightlyPrice = Math.max(0, item.basePrice - discountAmountPerNight)
      item.isMemberRate = discountPercent > 0
      item.appliedTier = discountPercent > 0 ? memberTier : null
    })
  }

  // Aggregate metrics
  const cartCount = computed(() => {
    return cartItems.value.reduce((sum, item) => sum + item.quantity, 0)
  })

  const cartBaseSubtotal = computed(() => {
    return cartItems.value.reduce((sum, item) => {
      return sum + item.basePrice * item.nights * item.quantity
    }, 0)
  })

  const cartDiscountTotal = computed(() => {
    return cartItems.value.reduce((sum, item) => {
      return sum + item.discountAmountPerNight * item.nights * item.quantity
    }, 0)
  })

  const cartNetSubtotal = computed(() => {
    return Math.max(0, cartBaseSubtotal.value - cartDiscountTotal.value)
  })

  const cartTaxValue = computed(() => {
    return Math.round((cartNetSubtotal.value * 11) / 100)
  })

  const cartServiceValue = computed(() => {
    return Math.round((cartNetSubtotal.value * 10) / 100)
  })

  const cartGrandTotal = computed(() => {
    return cartNetSubtotal.value + cartTaxValue.value + cartServiceValue.value
  })

  const cartEstimatedPoints = computed(() => {
    // Dynamic real-time calculation based on the active point plan
    const { calculatePoints, pointPlan } = useAuth()
    if (pointPlan.value && Number(pointPlan.value.price) > 0) {
      return calculatePoints(cartNetSubtotal.value)
    }
    return Math.floor(cartNetSubtotal.value / 500000)
  })

  return {
    cartItems,
    isCartOpen,
    isCheckoutModalOpen,
    cartCount,
    cartBaseSubtotal,
    cartDiscountTotal,
    cartNetSubtotal,
    cartTaxValue,
    cartServiceValue,
    cartGrandTotal,
    cartEstimatedPoints,
    addToCart,
    removeFromCart,
    updateQuantity,
    updateNights,
    updateGuests,
    clearCart,
    toggleCart,
    openCart,
    closeCart,
    openCheckout,
    closeCheckout,
    refreshCartMemberRates,
  }
}
