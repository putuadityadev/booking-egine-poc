<template>
  <div v-if="isOpen" class="guest-picker-popover" @click.stop>
    <div class="stepper-row">
      <div class="stepper-info">
        <div class="stepper-title">Adults</div>
        <div class="stepper-sub">Age 13+</div>
      </div>
      <div class="stepper-controls">
        <button class="step-btn" :disabled="adults <= 1" @click="adults--">-</button>
        <span class="step-val">{{ adults }}</span>
        <button class="step-btn" :disabled="totalGuests >= maxGuests" @click="adults++">+</button>
      </div>
    </div>

    <div class="row-divider"></div>

    <div class="stepper-row">
      <div class="stepper-info">
        <div class="stepper-title">Children</div>
        <div class="stepper-sub">Ages 2–12</div>
      </div>
      <div class="stepper-controls">
        <button class="step-btn" :disabled="children <= 0" @click="children--">-</button>
        <span class="step-val">{{ children }}</span>
        <button class="step-btn" :disabled="totalGuests >= maxGuests" @click="children++">+</button>
      </div>
    </div>

    <div class="row-divider"></div>

    <div class="stepper-row">
      <div class="stepper-info">
        <div class="stepper-title">Infants</div>
        <div class="stepper-sub">Under 2</div>
      </div>
      <div class="stepper-controls">
        <button class="step-btn" :disabled="infants <= 0" @click="infants--">-</button>
        <span class="step-val">{{ infants }}</span>
        <button class="step-btn" :disabled="infants >= 4" @click="infants++">+</button>
      </div>
    </div>

    <div class="picker-footer">
      <span class="guest-summary-text">{{ totalGuests }} Guests</span>
      <button class="btn-primary btn-sm" @click="applyGuests">Apply</button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  modelValue: {
    type: Number,
    default: 2,
  },
  maxGuests: {
    type: Number,
    default: 6,
  },
})

const emit = defineEmits(['close', 'update:modelValue'])

const adults = ref(props.modelValue > 0 ? props.modelValue : 2)
const children = ref(0)
const infants = ref(0)

const totalGuests = computed(() => adults.value + children.value)

const applyGuests = () => {
  emit('update:modelValue', totalGuests.value)
  emit('close')
}
</script>

<style scoped>
.guest-picker-popover {
  position: absolute;
  top: calc(100% + 12px);
  right: 0;
  width: 320px;
  background: var(--colors-canvas);
  border-radius: var(--radius-lg);
  border: 1px solid var(--colors-hairline);
  box-shadow: none;
  padding: 20px;
  z-index: 150;
  animation: popoverFadeIn 0.2s ease-out;
}

@keyframes popoverFadeIn {
  from { opacity: 0; transform: translateY(-8px); }
  to { opacity: 1; transform: translateY(0); }
}

.stepper-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 0;
}

.stepper-title {
  font-size: 15px;
  font-weight: 700;
  color: var(--colors-ink);
}

.stepper-sub {
  font-size: 12px;
  color: var(--colors-muted);
}

.stepper-controls {
  display: flex;
  align-items: center;
  gap: 12px;
}

.step-btn {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  border: 1px solid var(--colors-border-strong);
  background: var(--colors-canvas);
  color: var(--colors-body);
  font-size: 16px;
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.15s ease;
}

.step-btn:hover:not(:disabled) {
  border-color: var(--colors-ink);
  color: var(--colors-ink);
}

.step-btn:disabled {
  opacity: 0.3;
  cursor: not-allowed;
}

.step-val {
  font-size: 14px;
  font-weight: 700;
  color: var(--colors-ink);
  width: 16px;
  text-align: center;
}

.row-divider {
  height: 1px;
  background: var(--colors-hairline-soft);
  margin: 8px 0;
}

.picker-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 14px;
  margin-top: 8px;
  border-top: 1px solid var(--colors-hairline-soft);
}

.guest-summary-text {
  font-size: 13px;
  font-weight: 600;
  color: var(--colors-muted);
}

.btn-sm {
  padding: 6px 16px;
  font-size: 13px;
}
</style>
