<template>
  <div class="booking-view-root">
    <!-- Top Navigation Bar -->
    <Navbar />

    <!-- Global Pill Search Bar with Custom Date/Guest Pickers -->
    <SearchBar />

    <!-- Main Content Body -->
    <main class="container booking-main">
      <!-- Loading State -->
      <div v-if="isLoadingProperty && !activeProperty" class="loading-state">
        <div class="spinner"></div>
        <p>Loading luxury sanctuary...</p>
      </div>

      <template v-else-if="activeProperty">
        <!-- Property Header & Hero Mosaic + Rich Member Incentive Banner -->
        <PropertyHeader :property="activeProperty" />

        <!-- Stays Section (Suites & Villas) -->
        <section v-if="activeTab === 'stays'" id="rooms-section" class="catalog-section">
          <div class="section-heading-row">
            <div>
              <h2 class="section-title">Available Suites & Villas</h2>
              <p class="section-subtitle">
                Prices include all luxury inclusions, daily breakfast, and personal butler service.
                <span v-if="isLoggedIn" class="member-highlight-note">
                  Your {{ memberTier }} tier discount is automatically applied!
                </span>
                <span v-else class="guest-action-note" @click="openAuthModal('signin', 'otp')">
                  Sign in to reveal exclusive Member Rates.
                </span>
              </p>
            </div>

            <div class="room-count-badge">
              {{ activeProperty.rooms?.length || 0 }} Room Suites Available
            </div>
          </div>

          <!-- Rooms Grid -->
          <div class="rooms-grid">
            <RoomCard
              v-for="room in activeProperty.rooms"
              :key="room.id"
              :room="room"
              @reserve="handleOpenReserveModal"
            />
          </div>
        </section>

        <!-- Experiences Section -->
        <section v-if="activeTab === 'experiences'" class="catalog-section">
          <div class="section-heading-row">
            <div>
              <h2 class="section-title">Curated Resort Experiences & Spa</h2>
              <p class="section-subtitle">
                Immerse yourself in authentic Balinese wellness rituals, riverside dining, and cultural excursions.
              </p>
            </div>
          </div>

          <div class="experiences-grid">
            <ExperienceCard
              v-for="exp in activeProperty.experiences"
              :key="exp.id"
              :experience="exp"
            />
          </div>
        </section>

        <!-- Verified Guest & Member Reviews Section -->
        <section class="reviews-section">
          <div class="reviews-header-row">
            <div>
              <div class="reviews-eyebrow">Verified Sanctuary Experiences</div>
              <h3 class="reviews-title">Guest &amp; Member Reviews</h3>
            </div>
            <div class="reviews-score-badge">
              <div class="score-stars">
                <svg
                  v-for="s in 5"
                  :key="s"
                  viewBox="0 0 24 24"
                  fill="currentColor"
                  class="star-svg"
                >
                  <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                </svg>
              </div>
              <span class="score-num">4.98</span>
              <span class="score-meta">&bull; 148 Verified Stays</span>
            </div>
          </div>

          <div class="reviews-grid">
            <article
              v-for="rev in memberReviews"
              :key="rev.id"
              class="review-card"
            >
              <div class="review-card-top">
                <div class="reviewer-info">
                  <div class="reviewer-avatar" :class="rev.tierClass">
                    {{ rev.initials }}
                  </div>
                  <div class="reviewer-details">
                    <div class="reviewer-name-row">
                      <h4 class="reviewer-name">{{ rev.guestName }}</h4>
                      <span class="verified-pill">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="verified-check-svg">
                          <polyline points="20 6 9 17 4 12" />
                        </svg>
                        Verified
                      </span>
                    </div>
                    <div class="reviewer-meta-row">
                      <span class="review-tier-badge" :class="rev.tierClass">{{ rev.tier }}</span>
                      <span class="review-date-dot">&bull;</span>
                      <span class="review-date">{{ rev.stayDate }}</span>
                    </div>
                  </div>
                </div>
              </div>

              <div class="review-room-tag">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="bed-svg">
                  <path d="M2 4v16M2 8h18a2 2 0 0 1 2 2v10M2 17h20M6 8v9" />
                </svg>
                <span>{{ rev.roomStayed }}</span>
              </div>

              <div class="review-stars-row">
                <svg
                  v-for="s in rev.rating"
                  :key="s"
                  viewBox="0 0 24 24"
                  fill="currentColor"
                  class="card-star-svg"
                >
                  <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                </svg>
              </div>

              <h5 class="review-headline">"{{ rev.title }}"</h5>
              <p class="review-comment">{{ rev.comment }}</p>
            </article>
          </div>
        </section>
      </template>
    </main>

    <!-- Floating Member Perks Toast (Bottom-Left Minimal Widget) -->
    <FloatingPerksToast />

    <!-- Floating Reservation Cart Drawer (Bottom-Right Minimal Widget) -->
    <FloatingCartDrawer />

    <!-- Modals -->
    <BookingModal />
    <ConfirmationModal />
    <AuthModal />

    <!-- Airbnb Light Footer -->
    <Footer />
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const memberReviews = ref([
  {
    id: 1,
    guestName: 'Elena Rostova',
    initials: 'ER',
    tier: 'Diamond Member',
    tierClass: 'tier-diamond',
    rating: 5,
    stayDate: 'August 2026',
    roomStayed: 'Royal Riverfront Pool Villa',
    title: 'Absolute bliss and seamless loyalty perks',
    comment:
      'The riverfront pool villa was sheer perfection. Our Diamond member benefits were honored immediately with early check-in, complimentary afternoon tea, and instantaneous point accumulation. Best sanctuary experience in Bali.',
    verified: true,
  },
  {
    id: 2,
    guestName: 'Marcus Vance',
    initials: 'MV',
    tier: 'Platinum Member',
    tierClass: 'tier-platinum',
    rating: 5,
    stayDate: 'July 2026',
    roomStayed: 'Grand Valley Suite',
    title: 'Flawless hospitality & magnificent valley views',
    comment:
      'From the private butler greeting to the morning floating breakfast, every second felt thoughtfully curated. Member discount saved us over IDR 1,500,000 on direct direct booking!',
    verified: true,
  },
  {
    id: 3,
    guestName: 'Dr. Dian Sastrowardoyo',
    initials: 'DS',
    tier: 'Gold Member',
    tierClass: 'tier-gold',
    rating: 5,
    stayDate: 'July 2026',
    roomStayed: 'Ayung Sanctuary Pool Villa',
    title: 'Unrivaled tranquil luxury with genuine warmth',
    comment:
      'A true haven of tranquility. The botanical spa rituals and private plunge pool overlooking the jungle ravine are world-class. Points were automatically added to my account before checkout.',
    verified: true,
  },
])
import Navbar from '../components/Navbar.vue'
import SearchBar from '../components/SearchBar.vue'
import PropertyHeader from '../components/PropertyHeader.vue'
import RoomCard from '../components/RoomCard.vue'
import ExperienceCard from '../components/ExperienceCard.vue'
import FloatingPerksToast from '../components/FloatingPerksToast.vue'
import FloatingCartDrawer from '../components/FloatingCartDrawer.vue'
import AuthModal from '../components/AuthModal.vue'
import BookingModal from '../components/BookingModal.vue'
import ConfirmationModal from '../components/ConfirmationModal.vue'
import Footer from '../components/Footer.vue'

import { useAuth } from '../composables/useAuth'
import { useBooking } from '../composables/useBooking'

const route = useRoute()
const router = useRouter()

const { isLoggedIn, memberTier, openAuthModal, fetchPropertyContext } = useAuth()
const {
  activeProperty,
  isLoadingProperty,
  activeTab,
  fetchProperties,
  fetchPropertyDetails,
  openBookingModal,
} = useBooking()

const loadPropertyFromRoute = async () => {
  const propertyId = parseInt(route.params.propertyId || '5', 10)
  await fetchPropertyDetails(propertyId)
  await fetchPropertyContext(propertyId)
}

watch(
  () => route.params.propertyId,
  async (newId) => {
    if (newId) {
      await loadPropertyFromRoute()
    }
  }
)

onMounted(async () => {
  await fetchProperties()
  await loadPropertyFromRoute()
})

const handleOpenReserveModal = (room) => {
  openBookingModal(room)
}
</script>

<style scoped>
.booking-view-root {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background-color: var(--colors-canvas);
}

.booking-main {
  flex: 1;
}

.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 80px 0;
  color: var(--colors-muted);
}

.spinner {
  width: 40px;
  height: 40px;
  border: 3px solid var(--colors-hairline);
  border-top-color: var(--colors-primary);
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin-bottom: 16px;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.catalog-section {
  margin-bottom: 48px;
}

.section-heading-row {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  margin-bottom: 24px;
}

.section-title {
  font-size: 24px;
  font-weight: 700;
  color: var(--colors-ink);
  letter-spacing: -0.3px;
  margin-bottom: 4px;
}

.section-subtitle {
  font-size: 14px;
  color: var(--colors-muted);
}

.member-highlight-note {
  color: #6d28d9;
  font-weight: 600;
}

.guest-action-note {
  color: var(--colors-primary);
  font-weight: 600;
  cursor: pointer;
}

.guest-action-note:hover {
  text-decoration: underline;
}

.room-count-badge {
  font-size: 12px;
  font-weight: 600;
  background: var(--colors-surface-soft);
  color: var(--colors-muted);
  padding: 6px 12px;
  border-radius: var(--radius-full);
}

/* Rooms Grid */
.rooms-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
}

/* Experiences Grid */
.experiences-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
}

/* Reviews Section */
.reviews-section {
  border-top: 1px solid var(--colors-hairline-soft, #e2e8f0);
  padding-top: 48px;
  margin-bottom: 56px;
}

.reviews-header-row {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  margin-bottom: 28px;
  flex-wrap: wrap;
  gap: 16px;
}

.reviews-eyebrow {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 1.2px;
  color: #c9a840;
  margin-bottom: 6px;
}

.reviews-title {
  font-size: 24px;
  font-weight: 800;
  letter-spacing: -0.4px;
  color: var(--colors-ink, #0f172a);
  margin: 0;
}

.reviews-score-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: #ffffff;
  border: 1px solid var(--colors-hairline, #e2e8f0);
  padding: 8px 16px;
  border-radius: 999px;
}

.score-stars {
  display: flex;
  gap: 2px;
}

.star-svg {
  width: 14px;
  height: 14px;
  color: #f59e0b;
}

.score-num {
  font-size: 14px;
  font-weight: 800;
  color: #0f172a;
}

.score-meta {
  font-size: 12px;
  color: #64748b;
  font-weight: 500;
}

.reviews-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
}

.review-card {
  background: #ffffff;
  border: 1px solid var(--colors-hairline, #e2e8f0);
  border-radius: 14px;
  padding: 24px;
  display: flex;
  flex-direction: column;
  transition: transform 0.2s ease, border-color 0.2s ease;
}

.review-card:hover {
  transform: translateY(-2px);
  border-color: #cbd5e1;
}

.review-card-top {
  margin-bottom: 14px;
}

.reviewer-info {
  display: flex;
  align-items: center;
  gap: 12px;
}

.reviewer-avatar {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
  font-size: 14px;
  color: #ffffff;
  flex-shrink: 0;
}

.reviewer-avatar.tier-diamond {
  background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
  border: 1.5px solid #c9a840;
}

.reviewer-avatar.tier-platinum {
  background: linear-gradient(135deg, #334155 0%, #1e293b 100%);
  border: 1.5px solid #94a3b8;
}

.reviewer-avatar.tier-gold {
  background: linear-gradient(135deg, #b45309 0%, #78350f 100%);
  border: 1.5px solid #f59e0b;
}

.reviewer-details {
  flex: 1;
  min-width: 0;
}

.reviewer-name-row {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 2px;
}

.reviewer-name {
  font-size: 14px;
  font-weight: 700;
  color: #0f172a;
  margin: 0;
}

.verified-pill {
  display: inline-flex;
  align-items: center;
  gap: 3px;
  font-size: 11px;
  font-weight: 600;
  color: #16a34a;
  background: #f0fdf4;
  padding: 1px 6px;
  border-radius: 12px;
  border: 1px solid #bbf7d0;
}

.verified-check-svg {
  width: 10px;
  height: 10px;
}

.reviewer-meta-row {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
}

.review-tier-badge {
  font-weight: 700;
  font-size: 11px;
}

.review-tier-badge.tier-diamond {
  color: #0f172a;
}

.review-tier-badge.tier-platinum {
  color: #475569;
}

.review-tier-badge.tier-gold {
  color: #b45309;
}

.review-date-dot {
  color: #94a3b8;
}

.review-date {
  color: #64748b;
}

.review-room-tag {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  font-weight: 600;
  color: #475569;
  background: #f8fafc;
  padding: 4px 10px;
  border-radius: 6px;
  border: 1px solid #f1f5f9;
  margin-bottom: 12px;
  width: fit-content;
}

.bed-svg {
  width: 13px;
  height: 13px;
  color: #64748b;
}

.review-stars-row {
  display: flex;
  gap: 2px;
  margin-bottom: 10px;
}

.card-star-svg {
  width: 14px;
  height: 14px;
  color: #f59e0b;
}

.review-headline {
  font-size: 14px;
  font-weight: 700;
  color: #0f172a;
  margin: 0 0 8px 0;
  line-height: 1.4;
}

.review-comment {
  font-size: 13px;
  color: #475569;
  line-height: 1.6;
  margin: 0;
  flex: 1;
}

@media (max-width: 1180px) {
  .rooms-grid {
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
  }
}

@media (max-width: 860px) {
  .rooms-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 14px;
  }
}

@media (max-width: 580px) {
  .rooms-grid,
  .experiences-grid {
    grid-template-columns: 1fr;
    gap: 14px;
  }
}
</style>
