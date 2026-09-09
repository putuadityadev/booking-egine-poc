<template>
  <div v-if="isOpen" class="airbnb-datepicker-popover" @click.stop>
    <!-- Header with selection hint -->
    <div class="datepicker-header">
      <div>
        <h4 class="picker-title">
          <span v-if="!tempCheckIn">Select check-in date</span>
          <span v-else-if="!tempCheckOut">Select check-out date</span>
          <span v-else>{{ selectedNights }} Nights in {{ destinationCity }}</span>
        </h4>
        <p class="picker-subtitle">
          <span v-if="tempCheckIn && tempCheckOut">
            {{ formatDate(tempCheckIn) }} – {{ formatDate(tempCheckOut) }}
          </span>
          <span v-else-if="tempCheckIn">
            Check-in: {{ formatDate(tempCheckIn) }} • Select your check-out
          </span>
          <span v-else>
            Add your travel dates for exact luxury pricing
          </span>
        </p>
      </div>

      <!-- Month Navigation Arrows -->
      <div class="nav-arrows">
        <button class="arrow-btn" :disabled="isPrevMonthDisabled" @click="prevMonth" title="Previous Month">
          ‹
        </button>
        <button class="arrow-btn" @click="nextMonth" title="Next Month">
          ›
        </button>
      </div>
    </div>

    <!-- Dual Month Calendar Grid -->
    <div class="calendar-months-grid">
      <!-- Month 1 -->
      <div class="month-card">
        <h5 class="month-name">{{ monthNames[currentMonth1.month] }} {{ currentMonth1.year }}</h5>
        <div class="weekdays-row">
          <span v-for="w in weekdays" :key="w">{{ w }}</span>
        </div>
        <div class="days-grid">
          <button
            v-for="(cell, idx) in daysMonth1"
            :key="idx"
            class="day-cell"
            :class="getDayClasses(cell)"
            :disabled="cell.isPast || cell.isEmpty"
            @click="handleDayClick(cell)"
            @mouseenter="handleDayHover(cell)"
          >
            <span v-if="!cell.isEmpty" class="day-number">{{ cell.dayNumber }}</span>
          </button>
        </div>
      </div>

      <!-- Month 2 -->
      <div class="month-card second-month">
        <h5 class="month-name">{{ monthNames[currentMonth2.month] }} {{ currentMonth2.year }}</h5>
        <div class="weekdays-row">
          <span v-for="w in weekdays" :key="w">{{ w }}</span>
        </div>
        <div class="days-grid">
          <button
            v-for="(cell, idx) in daysMonth2"
            :key="idx"
            class="day-cell"
            :class="getDayClasses(cell)"
            :disabled="cell.isPast || cell.isEmpty"
            @click="handleDayClick(cell)"
            @mouseenter="handleDayHover(cell)"
          >
            <span v-if="!cell.isEmpty" class="day-number">{{ cell.dayNumber }}</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Footer Actions -->
    <div class="datepicker-footer">
      <button class="btn-clear" @click="clearDates">Clear dates</button>
      <div class="footer-right-actions">
        <button class="btn-secondary btn-sm" @click="$emit('close')">Cancel</button>
        <button class="btn-primary btn-sm" :disabled="!tempCheckIn || !tempCheckOut" @click="applyDates">
          Apply Dates
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  modelCheckIn: {
    type: String,
    default: '',
  },
  modelCheckOut: {
    type: String,
    default: '',
  },
  destinationCity: {
    type: String,
    default: 'Bali',
  },
})

const emit = defineEmits(['close', 'apply'])

const weekdays = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa']
const monthNames = [
  'January', 'February', 'March', 'April', 'May', 'June',
  'July', 'August', 'September', 'October', 'November', 'December'
]

// Current active viewing month offset from today
const baseDate = new Date()
const viewMonth = ref(baseDate.getMonth())
const viewYear = ref(baseDate.getFullYear())

const tempCheckIn = ref(props.modelCheckIn)
const tempCheckOut = ref(props.modelCheckOut)
const hoveredDate = ref(null)

watch(() => props.isOpen, (open) => {
  if (open) {
    tempCheckIn.value = props.modelCheckIn
    tempCheckOut.value = props.modelCheckOut
    if (props.modelCheckIn) {
      const d = new Date(props.modelCheckIn)
      viewMonth.value = d.getMonth()
      viewYear.value = d.getFullYear()
    }
  }
})

const currentMonth1 = computed(() => ({
  month: viewMonth.value,
  year: viewYear.value,
}))

const currentMonth2 = computed(() => {
  let m = viewMonth.value + 1
  let y = viewYear.value
  if (m > 11) {
    m = 0
    y++
  }
  return { month: m, year: y }
})

const isPrevMonthDisabled = computed(() => {
  const today = new Date()
  return (
    viewYear.value === today.getFullYear() &&
    viewMonth.value <= today.getMonth()
  )
})

const prevMonth = () => {
  if (isPrevMonthDisabled.value) return
  if (viewMonth.value === 0) {
    viewMonth.value = 11
    viewYear.value--
  } else {
    viewMonth.value--
  }
}

const nextMonth = () => {
  if (viewMonth.value === 11) {
    viewMonth.value = 0
    viewYear.value++
  } else {
    viewMonth.value++
  }
}

const generateDays = (year, month) => {
  const firstDayIndex = new Date(year, month, 1).getDay()
  const daysInMonth = new Date(year, month + 1, 0).getDate()
  const todayStr = new Date().toISOString().split('T')[0]

  const cells = []

  // Empty leading days
  for (let i = 0; i < firstDayIndex; i++) {
    cells.push({ isEmpty: true })
  }

  // Days in month
  for (let d = 1; d <= daysInMonth; d++) {
    const mm = String(month + 1).padStart(2, '0')
    const dd = String(d).padStart(2, '0')
    const dateStr = `${year}-${mm}-${dd}`
    const isPast = dateStr < todayStr

    cells.push({
      isEmpty: false,
      dayNumber: d,
      dateString: dateStr,
      isPast,
    })
  }

  return cells
}

const daysMonth1 = computed(() => generateDays(currentMonth1.value.year, currentMonth1.value.month))
const daysMonth2 = computed(() => generateDays(currentMonth2.value.year, currentMonth2.value.month))

const selectedNights = computed(() => {
  if (!tempCheckIn.value || !tempCheckOut.value) return 0
  const d1 = new Date(tempCheckIn.value)
  const d2 = new Date(tempCheckOut.value)
  const diffDays = Math.ceil(Math.abs(d2 - d1) / (1000 * 60 * 60 * 24))
  return diffDays > 0 ? diffDays : 1
})

const formatDate = (isoStr) => {
  if (!isoStr) return ''
  const d = new Date(isoStr)
  return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

const handleDayClick = (cell) => {
  if (cell.isEmpty || cell.isPast) return
  const clicked = cell.dateString

  if (!tempCheckIn.value || (tempCheckIn.value && tempCheckOut.value)) {
    // Fresh selection
    tempCheckIn.value = clicked
    tempCheckOut.value = ''
  } else if (tempCheckIn.value && !tempCheckOut.value) {
    if (clicked <= tempCheckIn.value) {
      // Picked earlier date, shift checkIn
      tempCheckIn.value = clicked
    } else {
      // Set checkOut
      tempCheckOut.value = clicked
    }
  }
}

const handleDayHover = (cell) => {
  if (!cell.isEmpty && !cell.isPast && tempCheckIn.value && !tempCheckOut.value) {
    hoveredDate.value = cell.dateString
  }
}

const getDayClasses = (cell) => {
  if (cell.isEmpty) return 'empty'
  if (cell.isPast) return 'past'

  const date = cell.dateString
  const isStart = date === tempCheckIn.value
  const isEnd = date === tempCheckOut.value
  const isInRange = (
    tempCheckIn.value &&
    tempCheckOut.value &&
    date > tempCheckIn.value &&
    date < tempCheckOut.value
  )
  const isHoverInRange = (
    tempCheckIn.value &&
    !tempCheckOut.value &&
    hoveredDate.value &&
    date > tempCheckIn.value &&
    date <= hoveredDate.value
  )

  return {
    'day-start': isStart,
    'day-end': isEnd,
    'in-range': isInRange || isHoverInRange,
    'single-selected': isStart && !tempCheckOut.value,
  }
}

const clearDates = () => {
  tempCheckIn.value = ''
  tempCheckOut.value = ''
  hoveredDate.value = null
}

const applyDates = () => {
  if (tempCheckIn.value && tempCheckOut.value) {
    emit('apply', {
      checkIn: tempCheckIn.value,
      checkOut: tempCheckOut.value,
      nights: selectedNights.value,
    })
    emit('close')
  }
}
</script>

<style scoped>
.airbnb-datepicker-popover {
  position: absolute;
  top: calc(100% + 12px);
  left: 50%;
  transform: translateX(-50%);
  width: 760px;
  max-width: 95vw;
  background: var(--colors-canvas);
  border-radius: var(--radius-lg);
  border: 1px solid var(--colors-hairline);
  box-shadow: none;
  padding: 24px;
  z-index: 150;
  animation: popoverFadeIn 0.2s ease-out;
}

@keyframes popoverFadeIn {
  from { opacity: 0; transform: translate(-50%, -8px); }
  to { opacity: 1; transform: translate(-50%, 0); }
}

.datepicker-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 20px;
  padding-bottom: 16px;
  border-bottom: 1px solid var(--colors-hairline-soft);
}

.picker-title {
  font-size: 18px;
  font-weight: 700;
  color: var(--colors-ink);
  margin-bottom: 4px;
}

.picker-subtitle {
  font-size: 13px;
  color: var(--colors-muted);
}

.nav-arrows {
  display: flex;
  gap: 8px;
}

.arrow-btn {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  border: 1px solid var(--colors-hairline);
  background: var(--colors-canvas);
  font-size: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--colors-ink);
  transition: all 0.15s ease;
}

.arrow-btn:hover:not(:disabled) {
  border-color: var(--colors-ink);
  background: var(--colors-surface-soft);
}

.arrow-btn:disabled {
  opacity: 0.3;
  cursor: not-allowed;
}

/* 2 Months Side by Side */
.calendar-months-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 32px;
  margin-bottom: 20px;
}

.month-name {
  font-size: 15px;
  font-weight: 700;
  color: var(--colors-ink);
  text-align: center;
  margin-bottom: 14px;
}

.weekdays-row {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  text-align: center;
  font-size: 12px;
  font-weight: 600;
  color: var(--colors-muted);
  margin-bottom: 8px;
}

.days-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  row-gap: 4px;
}

.day-cell {
  height: 42px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  font-weight: 600;
  color: var(--colors-ink);
  position: relative;
  transition: all 0.15s ease;
  border-radius: 0;
}

.day-cell:hover:not(:disabled) .day-number {
  border: 1px solid var(--colors-ink);
}

.day-number {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2;
  transition: background-color 0.15s ease, color 0.15s ease;
}

.day-cell.day-start .day-number,
.day-cell.day-end .day-number,
.day-cell.single-selected .day-number {
  background-color: var(--colors-ink);
  color: #ffffff;
}

.day-cell.day-start {
  border-top-left-radius: 50%;
  border-bottom-left-radius: 50%;
}

.day-cell.day-end {
  border-top-right-radius: 50%;
  border-bottom-right-radius: 50%;
}

.day-cell.in-range {
  background-color: #fff1f2;
}

.day-cell.in-range .day-number {
  color: var(--colors-ink);
}

.day-cell.past {
  color: var(--colors-border-strong);
  cursor: not-allowed;
  text-decoration: line-through;
}

.day-cell.empty {
  cursor: default;
}

/* Footer */
.datepicker-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 16px;
  border-top: 1px solid var(--colors-hairline-soft);
}

.btn-clear {
  font-size: 13px;
  font-weight: 600;
  text-decoration: underline;
  color: var(--colors-ink);
}

.footer-right-actions {
  display: flex;
  gap: 10px;
}

.btn-sm {
  padding: 8px 16px;
  font-size: 13px;
}

@media (max-width: 744px) {
  .calendar-months-grid {
    grid-template-columns: 1fr;
  }
  .second-month {
    display: none;
  }
}
</style>
