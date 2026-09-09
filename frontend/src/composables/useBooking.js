import { ref, computed } from 'vue'

const properties = ref([])
const activePropertyId = ref(1)
const activeProperty = ref(null)
const isLoadingProperty = ref(false)
const activeTab = ref('stays') // 'stays' | 'experiences' | 'about'

// Search state
const checkIn = ref('2026-09-15')
const checkOut = ref('2026-09-17')
const guests = ref(2)
const destination = ref('Ubud, Bali')

// Booking modal & confirmation state
const selectedRoomForBooking = ref(null)
const isBookingModalOpen = ref(false)
const isConfirmationModalOpen = ref(false)
const confirmedReservation = ref(null)
const isSubmittingBooking = ref(false)
const bookingError = ref('')

export function useBooking() {
  const bffUrl = import.meta.env.VITE_BFF_API_URL || 'http://localhost:8002'

  const nights = computed(() => {
    if (!checkIn.value || !checkOut.value) return 1
    const d1 = new Date(checkIn.value)
    const d2 = new Date(checkOut.value)
    const diffTime = Math.abs(d2 - d1)
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
    return diffDays > 0 ? diffDays : 1
  })

  const fetchProperties = async () => {
    try {
      const res = await fetch(`${bffUrl}/api/properties`)
      const json = await res.json()
      if (json.success) {
        properties.value = json.data
      }
    } catch (err) {
      console.error('Failed to load properties:', err)
    }
  }

  const fetchPropertyDetails = async (id = activePropertyId.value) => {
    isLoadingProperty.value = true
    try {
      const res = await fetch(`${bffUrl}/api/properties/${id}`)
      const json = await res.json()
      if (json.success) {
        activeProperty.value = json.data
        activePropertyId.value = id
        destination.value = json.data.city || 'Ubud, Bali'
      }
    } catch (err) {
      console.error('Failed to load property details:', err)
    } finally {
      isLoadingProperty.value = false
    }
  }

  const switchProperty = async (id) => {
    if (activePropertyId.value === id && activeProperty.value) return
    await fetchPropertyDetails(id)
  }

  const openBookingModal = (room) => {
    selectedRoomForBooking.value = room
    bookingError.value = ''
    isBookingModalOpen.value = true
  }

  const closeBookingModal = () => {
    isBookingModalOpen.value = false
    selectedRoomForBooking.value = null
    bookingError.value = ''
  }

  const confirmReservation = async (bookingPayload) => {
    isSubmittingBooking.value = true
    bookingError.value = ''
    try {
      const res = await fetch(`${bffUrl}/api/booking/reserve`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify(bookingPayload),
      })

      const json = await res.json()
      if (!res.ok || !json.success) {
        throw new Error(json.message || 'Failed to complete reservation.')
      }

      confirmedReservation.value = json.data
      isBookingModalOpen.value = false
      isConfirmationModalOpen.value = true
      return json.data
    } catch (err) {
      bookingError.value = err.message
      throw err
    } finally {
      isSubmittingBooking.value = false
    }
  }

  const closeConfirmationModal = () => {
    isConfirmationModalOpen.value = false
  }

  return {
    properties,
    activePropertyId,
    activeProperty,
    isLoadingProperty,
    activeTab,
    checkIn,
    checkOut,
    guests,
    destination,
    nights,
    selectedRoomForBooking,
    isBookingModalOpen,
    isConfirmationModalOpen,
    confirmedReservation,
    isSubmittingBooking,
    bookingError,
    fetchProperties,
    fetchPropertyDetails,
    switchProperty,
    openBookingModal,
    closeBookingModal,
    confirmReservation,
    closeConfirmationModal,
  }
}
