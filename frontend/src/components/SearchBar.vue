<template>
  <div class="search-bar-container">
    <div class="search-bar-pill">
      <!-- Segment 1: Where -->
      <div class="search-field-segment">
        <label class="segment-label">Where</label>
        <div class="segment-value">{{ destination }}</div>
      </div>

      <div class="segment-divider"></div>

      <!-- Segment 2: Check in -->
      <div
        class="search-field-segment clickable"
        :class="{ active: isDatePickerOpen && activeDateField === 'checkIn' }"
        @click="openDatePicker('checkIn')"
      >
        <label class="segment-label">Check in</label>
        <div class="segment-value date-display">
          {{ formatDisplayDate(checkIn) || 'Add dates' }}
        </div>
      </div>

      <div class="segment-divider"></div>

      <!-- Segment 3: Check out -->
      <div
        class="search-field-segment clickable"
        :class="{ active: isDatePickerOpen && activeDateField === 'checkOut' }"
        @click="openDatePicker('checkOut')"
      >
        <label class="segment-label">Check out</label>
        <div class="segment-value date-display">
          {{ formatDisplayDate(checkOut) || 'Add dates' }}
        </div>
      </div>

      <div class="segment-divider"></div>

      <!-- Segment 4: Who -->
      <div
        class="search-field-segment clickable"
        :class="{ active: isGuestPickerOpen }"
        @click="isGuestPickerOpen = !isGuestPickerOpen; isDatePickerOpen = false"
      >
        <label class="segment-label">Who</label>
        <div class="segment-value">{{ guests }} Guests</div>
      </div>

      <!-- Search Orb Button -->
      <button class="search-orb" title="Search availability" @click="handleSearch">
        <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <circle cx="11" cy="11" r="8"></circle>
          <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
      </button>

      <!-- Custom Airbnb Date Range Picker Popover -->
      <AirbnbDatePicker
        :is-open="isDatePickerOpen"
        :model-check-in="checkIn"
        :model-check-out="checkOut"
        :destination-city="destination"
        @close="isDatePickerOpen = false"
        @apply="handleApplyDates"
      />

      <!-- Custom Airbnb Guest Picker Popover -->
      <AirbnbGuestPicker
        :is-open="isGuestPickerOpen"
        :model-value="guests"
        @close="isGuestPickerOpen = false"
        @update:model-value="guests = $event"
      />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useBooking } from '../composables/useBooking'
import AirbnbDatePicker from './AirbnbDatePicker.vue'
import AirbnbGuestPicker from './AirbnbGuestPicker.vue'

const { checkIn, checkOut, guests, destination } = useBooking()

const isDatePickerOpen = ref(false)
const isGuestPickerOpen = ref(false)
const activeDateField = ref('checkIn')

const openDatePicker = (field) => {
  activeDateField.value = field
  isDatePickerOpen.value = true
  isGuestPickerOpen.value = false
}

const formatDisplayDate = (isoStr) => {
  if (!isoStr) return ''
  const d = new Date(isoStr)
  return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
}

const handleApplyDates = (payload) => {
  checkIn.value = payload.checkIn
  checkOut.value = payload.checkOut
}

const handleSearch = () => {
  isDatePickerOpen.value = false
  isGuestPickerOpen.value = false
  const el = document.getElementById('rooms-section')
  if (el) {
    el.scrollIntoView({ behavior: 'smooth' })
  }
}
</script>

<style scoped>
.search-bar-container {
  display: flex;
  justify-content: center;
  margin: 24px 0 32px;
  padding: 0 16px;
  position: relative;
  z-index: 50;
}

.search-bar-pill {
  height: 66px;
  background: var(--colors-canvas);
  border-radius: var(--radius-full);
  border: 1px solid var(--colors-hairline);
  box-shadow: none;
  display: flex;
  align-items: center;
  padding-left: 20px;
  padding-right: 9px;
  max-width: 860px;
  width: 100%;
  position: relative;
  transition: border-color 0.2s ease;
}

.search-bar-pill:hover {
  border-color: var(--colors-ink);
}

.search-field-segment {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 10px 14px;
  min-width: 0;
  border-radius: var(--radius-full);
  transition: background-color 0.15s ease;
}

.search-field-segment.clickable {
  cursor: pointer;
}

.search-field-segment.clickable:hover {
  background-color: var(--colors-surface-soft);
}

.search-field-segment.active {
  background-color: var(--colors-surface-soft);
  box-shadow: none;
}

.segment-label {
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  color: var(--colors-ink);
  margin-bottom: 2px;
}

.segment-value {
  font-size: 14px;
  font-weight: 600;
  color: var(--colors-ink);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.date-display {
  color: var(--colors-ink);
}

.segment-divider {
  width: 1px;
  height: 32px;
  background: var(--colors-hairline);
  margin: 0 2px;
}

.search-orb {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background-color: var(--colors-primary);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--colors-on-primary);
  flex-shrink: 0;
  transition: background-color 0.2s ease, transform 0.1s ease;
  margin-left: 8px;
}

.search-orb:hover {
  background-color: var(--colors-primary-active);
  transform: scale(1.04);
}

.search-orb:active {
  transform: scale(0.96);
}

.search-icon {
  width: 18px;
  height: 18px;
}

@media (max-width: 744px) {
  .search-bar-pill {
    flex-wrap: wrap;
    height: auto;
    border-radius: var(--radius-md);
    padding: 16px;
    gap: 8px;
  }
  .segment-divider {
    display: none;
  }
  .search-field-segment {
    flex: 1 1 45%;
  }
  .search-orb {
    width: 100%;
    border-radius: var(--radius-sm);
    height: 44px;
    margin-left: 0;
    margin-top: 8px;
  }
}
</style>
