<template>
  <div class="room-card">
    <!-- Image Slider Container -->
    <div class="photo-container">
      <img
        :src="currentPhoto"
        :alt="room.name"
        class="room-image"
      />

      <!-- Floating Badges Overlay -->
      <div class="photo-overlay-top">
        <span class="badge-guest-favorite">Guest favorite</span>
        <button
          class="heart-btn"
          :class="{ active: isLiked }"
          @click.stop="isLiked = !isLiked"
          title="Save to wishlist"
        >
          <svg viewBox="0 0 24 24" fill="currentColor" class="heart-svg">
            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
          </svg>
        </button>
      </div>

      <!-- Carousel Dots Navigation -->
      <div v-if="allPhotos.length > 1" class="carousel-dots">
        <button
          v-for="(_, idx) in allPhotos"
          :key="idx"
          class="dot"
          :class="{ active: currentPhotoIndex === idx }"
          @click.stop="currentPhotoIndex = idx"
        ></button>
      </div>

      <!-- Navigation Arrows (Zero Shadow, Hairline Outline) -->
      <button
        v-if="allPhotos.length > 1 && currentPhotoIndex > 0"
        class="nav-arrow prev"
        @click.stop="currentPhotoIndex--"
        title="Previous photo"
        aria-label="Previous photo"
      >
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="arrow-svg">
          <path d="m15 18-6-6 6-6" />
        </svg>
      </button>
      <button
        v-if="allPhotos.length > 1 && currentPhotoIndex < allPhotos.length - 1"
        class="nav-arrow next"
        @click.stop="currentPhotoIndex++"
        title="Next photo"
        aria-label="Next photo"
      >
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="arrow-svg">
          <path d="m9 18 6-6-6-6" />
        </svg>
      </button>
    </div>

    <!-- Room Metadata & Booking Action -->
    <div class="room-details">
      <div class="title-row">
        <h3 class="room-title">{{ room.name }}</h3>
        <div class="capacity-pill">{{ room.capacity }} Guests</div>
      </div>

      <div class="room-spec-row">
        <span>{{ room.bed_type }}</span>
        <span>•</span>
        <span>{{ room.size_sqm }} m²</span>
        <span>•</span>
        <span class="free-cancel-tag">Free cancellation</span>
      </div>

      <!-- Features Tag Strip -->
      <div class="features-strip">
        <span v-for="(feat, idx) in (room.features || []).slice(0, 3)" :key="idx" class="feature-tag">
          {{ feat }}
        </span>
      </div>

      <div class="divider"></div>

      <!-- Pricing & Add to Cart Row -->
      <div class="pricing-reserve-row">
        <div class="pricing-block">
          <!-- Member Logged In Pricing -->
          <template v-if="isLoggedIn">
            <div class="strikethrough-price">
              IDR {{ formatCurrency(room.base_price) }}
            </div>
            <div class="final-price-row">
              <span class="price-value">IDR {{ formatCurrency(memberPrice) }}</span>
              <span class="price-unit">/ night</span>
            </div>
            <div class="loyalty-perk-chip">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="perk-svg">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
              </svg>
              <span>{{ memberTier }} ({{ tierDiscountPercent }}% off) • +{{ estimatedPoints }} Pts</span>
            </div>
          </template>

          <!-- Guest Pricing (Public rate strikethrough + Prominent Special Member Rate) -->
          <template v-else>
            <div class="strikethrough-price">
              IDR {{ formatCurrency(room.base_price) }}
            </div>
            <div class="final-price-row">
              <span class="price-value highlight-member-rate">IDR {{ formatCurrency(potentialDiscountedPrice) }}</span>
              <span class="price-unit">/ night</span>
            </div>
            <div class="guest-member-hint" @click="openAuthModal('signin')">
              <span class="member-tag">SPECIAL MEMBER PRICE</span>
              <span class="member-callout">Sign in to unlock</span>
            </div>
          </template>
        </div>

        <button class="btn-primary btn-reserve" @click="handleSelectRoom">
          <span>Reserve</span>
          <svg class="arrow-btn-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 12h14" />
            <path d="m12 5 7 7-7 7" />
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useAuth } from '../composables/useAuth'
import { useCart } from '../composables/useCart'
import { useBooking } from '../composables/useBooking'

const props = defineProps({
  room: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['reserve'])

const { isLoggedIn, memberTier, openAuthModal, calculatePoints } = useAuth()
const { cartItems } = useCart()
const { activeProperty, checkIn, checkOut, nights, guests } = useBooking()

const isLiked = ref(false)
const currentPhotoIndex = ref(0)

const isItemInCart = computed(() => {
  return cartItems.value.some((item) => item.roomId === props.room.id)
})

const handleSelectRoom = () => {
  emit('reserve', props.room)
}

const allPhotos = computed(() => {
  const list = []
  if (props.room.image_url) list.push(props.room.image_url)
  if (Array.isArray(props.room.gallery)) {
    props.room.gallery.forEach(img => {
      if (img && !list.includes(img)) list.push(img)
    })
  }
  return list.length > 0 ? list : ['https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=800&q=80']
})

const currentPhoto = computed(() => {
  return allPhotos.value[currentPhotoIndex.value] || allPhotos.value[0]
})

const tierDiscountPercent = computed(() => {
  const tier = memberTier.value || 'Diamond'
  const rates = props.room.tier_discount_rates || { Bronze: 5, Silver: 10, Gold: 15, Diamond: 20 }
  return rates[tier] || 20
})

const memberPrice = computed(() => {
  const base = parseFloat(props.room.base_price)
  const discount = (base * tierDiscountPercent.value) / 100
  return Math.max(0, base - discount)
})

const potentialDiscountedPrice = computed(() => {
  const base = parseFloat(props.room.base_price)
  return Math.max(0, base * 0.8)
})

const estimatedPoints = computed(() => {
  return calculatePoints(memberPrice.value)
})

const formatCurrency = (val) => {
  if (isNaN(val)) return '0'
  return new Intl.NumberFormat('id-ID').format(Math.round(val))
}
</script>

<style scoped>
.room-card {
  background: var(--colors-canvas);
  border-radius: 12px;
  border: 1px solid var(--colors-hairline-soft);
  overflow: hidden;
  box-shadow: none;
  display: flex;
  flex-direction: column;
  transition: border-color 0.2s ease, transform 0.2s ease;
}

.room-card:hover {
  border-color: var(--colors-border-strong);
  transform: translateY(-2px);
}

/* Photo Box */
.photo-container {
  position: relative;
  width: 100%;
  aspect-ratio: 16 / 10;
  background-color: var(--colors-surface-soft);
  overflow: hidden;
}

.room-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.photo-overlay-top {
  position: absolute;
  top: 12px;
  left: 12px;
  right: 12px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  z-index: 2;
}

.heart-btn {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: rgba(0, 0, 0, 0.35);
  color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  border: none;
  cursor: pointer;
  transition: transform 0.15s ease, background 0.2s ease;
}

.heart-btn:hover {
  transform: scale(1.08);
}

.heart-btn.active {
  background: var(--colors-canvas);
  color: var(--colors-primary);
}

.heart-svg {
  width: 16px;
  height: 16px;
}

/* Navigation Arrows */
.nav-arrow {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.95);
  border: 1px solid var(--colors-hairline-soft);
  color: var(--colors-ink);
  font-size: 16px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: none;
  z-index: 2;
  opacity: 0;
  cursor: pointer;
  transition: opacity 0.2s ease, border-color 0.15s ease;
}

.room-card:hover .nav-arrow {
  opacity: 1;
}

.nav-arrow:hover {
  border-color: var(--colors-ink);
}

.nav-arrow.prev {
  left: 10px;
}

.nav-arrow.next {
  right: 10px;
}

/* Dots */
.carousel-dots {
  position: absolute;
  bottom: 10px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  gap: 5px;
  z-index: 2;
}

.dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  border: none;
  background: rgba(255, 255, 255, 0.6);
  cursor: pointer;
  transition: background 0.2s ease, transform 0.2s ease;
}

.dot.active {
  background: #ffffff;
  transform: scale(1.3);
}

/* Details */
.room-details {
  padding: 14px 14px 16px 14px;
  display: flex;
  flex-direction: column;
  flex: 1;
}

.title-row {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 6px;
  margin-bottom: 4px;
}

.room-title {
  font-size: 15px;
  font-weight: 700;
  color: var(--colors-ink);
  line-height: 1.25;
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.capacity-pill {
  font-size: 10px;
  font-weight: 600;
  color: var(--colors-muted);
  background: var(--colors-surface-soft);
  border: 1px solid var(--colors-hairline-soft);
  padding: 2px 6px;
  border-radius: var(--radius-full);
  white-space: nowrap;
}

.room-spec-row {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 11.5px;
  color: var(--colors-muted);
  margin-bottom: 8px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.free-cancel-tag {
  color: #15803d;
  font-weight: 500;
}

.features-strip {
  display: flex;
  flex-wrap: wrap;
  gap: 4px;
  margin-bottom: 10px;
}

.feature-tag {
  font-size: 10px;
  font-weight: 500;
  background: var(--colors-surface-soft);
  color: var(--colors-body);
  border: 1px solid var(--colors-hairline-soft);
  padding: 2px 6px;
  border-radius: var(--radius-full);
}

.divider {
  height: 1px;
  background: var(--colors-hairline-soft);
  margin-top: auto;
  margin-bottom: 12px;
}

/* Pricing & CTA */
.pricing-reserve-row {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  gap: 6px;
}

.pricing-block {
  display: flex;
  flex-direction: column;
  min-width: 0;
  flex: 1;
}

.strikethrough-price {
  font-size: 11px;
  color: var(--colors-muted-soft);
  text-decoration: line-through;
}

.final-price-row {
  display: flex;
  align-items: baseline;
  gap: 3px;
}

.price-value {
  font-size: 16px;
  font-weight: 700;
  color: var(--colors-ink);
  white-space: nowrap;
}

.price-unit {
  font-size: 11px;
  color: var(--colors-muted);
  white-space: nowrap;
}

.loyalty-perk-chip {
  display: inline-flex;
  align-items: center;
  gap: 3px;
  font-size: 9.5px;
  font-weight: 600;
  color: #6d28d9;
  background: #ede9fe;
  padding: 2px 6px;
  border-radius: 4px;
  margin-top: 3px;
  max-width: 100%;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.perk-svg {
  width: 11px;
  height: 11px;
  flex-shrink: 0;
}

.arrow-svg {
  width: 13px;
  height: 13px;
}

.guest-member-hint {
  font-size: 10px;
  color: var(--colors-primary);
  margin-top: 3px;
  cursor: pointer;
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 3px;
}

.highlight-member-rate {
  color: #ff385c !important;
  font-weight: 800;
}

.member-tag {
  font-size: 9px;
  font-weight: 700;
  letter-spacing: 0.3px;
  background: #fff1f2;
  color: #e11d48;
  padding: 1px 5px;
  border-radius: 4px;
  border: 1px solid rgba(225, 29, 72, 0.2);
}

.member-callout {
  text-decoration: underline;
  font-size: 10.5px;
  color: var(--colors-body);
}

.guest-member-hint:hover .member-callout {
  color: var(--colors-primary);
}

.btn-reserve {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 8px 12px;
  font-size: 12.5px;
  font-weight: 600;
  border-radius: 6px;
  box-shadow: none;
  white-space: nowrap;
  flex-shrink: 0;
}

.cart-btn-svg,
.arrow-btn-svg {
  width: 13px;
  height: 13px;
}
</style>
