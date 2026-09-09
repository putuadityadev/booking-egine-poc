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

        <!-- About Property & Host Concierge Section -->
        <section class="about-property-section">
          <div class="about-grid">
            <div class="about-main">
              <h3 class="about-title">About this Sanctuary</h3>
              <p class="about-text">{{ activeProperty.description }}</p>

              <h4 class="amenities-title">What this sanctuary offers</h4>
              <div class="amenities-grid">
                <div
                  v-for="(amenity, idx) in activeProperty.amenities"
                  :key="idx"
                  class="amenity-item"
                >
                  <svg class="amenity-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="20 6 9 17 4 12"></polyline>
                  </svg>
                  <span>{{ amenity }}</span>
                </div>
              </div>
            </div>

            <!-- Concierge & Host Profile Card -->
            <div class="host-card">
              <div class="host-header">
                <div class="host-avatar">
                  <span>{{ activeProperty.code ? activeProperty.code.substring(0, 2) : 'JH' }}</span>
                </div>
                <div>
                  <h4 class="host-name">Hosted by {{ activeProperty.name }}</h4>
                  <div class="superhost-badge">
                    <svg viewBox="0 0 24 24" fill="currentColor" class="superhost-star-svg">
                      <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                    </svg>
                    <span>Superhost • Luxury Hospitality Partner</span>
                  </div>
                </div>
              </div>

              <p class="host-bio">
                Dedicated 24/7 guest concierge and private butler service ensuring your Jeevawasa Club privileges and member rewards are seamlessly honored throughout your stay.
              </p>

              <div class="host-stats">
                <div class="stat-cell">
                  <span class="stat-num">100%</span>
                  <span class="stat-lbl">Response rate</span>
                </div>
                <div class="stat-cell">
                  <span class="stat-num">&lt; 1 hr</span>
                  <span class="stat-lbl">Response time</span>
                </div>
                <div class="stat-cell">
                  <span class="stat-num">Verified</span>
                  <span class="stat-lbl">Sanctuary Partner</span>
                </div>
              </div>
            </div>
          </div>
        </section>
      </template>
    </main>

    <!-- Floating Member Perks Toast (Bottom-Left Minimal Widget) -->
    <FloatingPerksToast />

    <!-- Floating Reservation Cart Drawer (Bottom-Right Minimal Widget) -->
    <FloatingCartDrawer />

    <!-- Modals -->
    <AuthModal />
    <BookingModal />
    <ConfirmationModal />

    <!-- Airbnb Light Footer -->
    <Footer />
  </div>
</template>

<script setup>
import { watch, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
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
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
}

/* Experiences Grid */
.experiences-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
}

/* About & Host Section */
.about-property-section {
  border-top: 1px solid var(--colors-hairline-soft);
  padding-top: 40px;
  margin-bottom: 48px;
}

.about-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 40px;
}

.about-title {
  font-size: 20px;
  font-weight: 700;
  color: var(--colors-ink);
  margin-bottom: 12px;
}

.about-text {
  font-size: 15px;
  line-height: 1.6;
  color: var(--colors-body);
  margin-bottom: 28px;
}

.amenities-title {
  font-size: 16px;
  font-weight: 700;
  color: var(--colors-ink);
  margin-bottom: 16px;
}

.amenities-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px 24px;
}

.amenity-item {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 14px;
  color: var(--colors-ink);
}

.amenity-check {
  width: 16px;
  height: 16px;
  color: var(--colors-primary);
}

/* Host Card */
.host-card {
  background: var(--colors-surface-soft);
  border: 1px solid var(--colors-hairline-soft);
  border-radius: var(--radius-md);
  padding: 24px;
  height: fit-content;
}

.host-header {
  display: flex;
  align-items: center;
  gap: 14px;
  margin-bottom: 16px;
}

.host-avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: var(--colors-primary);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 16px;
}

.host-name {
  font-size: 16px;
  font-weight: 700;
  color: var(--colors-ink);
}

.superhost-badge {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 12px;
  color: var(--colors-muted);
}

.superhost-star-svg {
  width: 12px;
  height: 12px;
  color: #f59e0b;
}

.host-bio {
  font-size: 13px;
  color: var(--colors-body);
  line-height: 1.5;
  margin-bottom: 20px;
}

.host-stats {
  display: flex;
  border-top: 1px solid var(--colors-hairline);
  padding-top: 14px;
  gap: 20px;
}

.stat-cell {
  display: flex;
  flex-direction: column;
}

.stat-num {
  font-size: 15px;
  font-weight: 700;
  color: var(--colors-ink);
}

.stat-lbl {
  font-size: 11px;
  color: var(--colors-muted);
}

@media (max-width: 1024px) {
  .rooms-grid,
  .experiences-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  .about-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 744px) {
  .rooms-grid,
  .experiences-grid {
    grid-template-columns: 1fr;
  }
}
</style>
