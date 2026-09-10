<template>
  <div v-if="isConfirmationModalOpen && confirmedReservation" class="modal-scrim" @click.self="closeConfirmationModal">
    <div class="confirmation-dialog-card">
      <div class="celebration-header">
        <div class="celebration-icon-ring">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="check-icon">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
        </div>
        <h3 class="celebration-title">Reservation Confirmed!</h3>
        <p class="celebration-subtitle">
          Your booking has been secured and synchronized with the Jeevawasa Platform.
        </p>
      </div>

      <div class="dialog-body">
        <!-- Reservation Code Badge -->
        <div class="reference-code-card">
          <span class="ref-label">Booking Reference Code</span>
          <span class="ref-code">{{ confirmedReservation.reservation_code }}</span>
          <span class="status-confirmed-pill">CONFIRMED</span>
        </div>

        <!-- Loyalty Points Awarded Card -->
        <div v-if="confirmedReservation.membership?.points_earned" class="points-awarded-card" :class="{ 'is-pending': !confirmedReservation.membership?.is_points_materialized }">
          <div class="points-sparkle-badge">
            <svg v-if="confirmedReservation.membership?.is_points_materialized" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="sparkle-svg">
              <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
            </svg>
            <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="sparkle-svg">
              <circle cx="12" cy="12" r="10" />
              <polyline points="12 6 12 12 16 14" />
            </svg>
          </div>
          <div class="points-info">
            <div class="points-title">
              <span v-if="confirmedReservation.membership?.is_points_materialized">
                +{{ confirmedReservation.membership.points_earned }} Club Points Awarded!
              </span>
              <span v-else>
                ⏳ +{{ confirmedReservation.membership.points_earned }} Club Points Pending
              </span>
            </div>
            <div class="points-desc">
              <span v-if="confirmedReservation.membership?.is_points_materialized">
                Successfully credited to your Jeevawasa Club account.
              </span>
              <span v-else>
                Points reserved. Will be automatically released to your account upon {{ confirmedReservation.membership.point_release_mode === 'checkin' ? 'check-in' : 'check-out' }}.
              </span>
            </div>
          </div>
        </div>

        <!-- Booking Summary Details -->
        <div class="details-summary-card">
          <div class="summary-line">
            <span class="line-label">Property</span>
            <span class="line-value">{{ confirmedReservation.property_name }}</span>
          </div>
          <div class="summary-line">
            <span class="line-label">Room Suite</span>
            <span class="line-value">{{ confirmedReservation.room_name }}</span>
          </div>
          <div class="summary-line">
            <span class="line-label">Dates</span>
            <span class="line-value">{{ confirmedReservation.check_in }} — {{ confirmedReservation.check_out }} ({{ confirmedReservation.nights }} Nights)</span>
          </div>
          <div class="summary-line">
            <span class="line-label">Guest</span>
            <span class="line-value">{{ confirmedReservation.guest_name }} ({{ confirmedReservation.guests }} Guests)</span>
          </div>
          <div class="summary-divider"></div>
          <div class="summary-line total-line">
            <span class="line-label">Total Amount Paid</span>
            <span class="total-amount">IDR {{ formatCurrency(confirmedReservation.pricing?.grand_total) }}</span>
          </div>
        </div>

        <button class="btn-primary btn-block btn-done" @click="closeConfirmationModal">
          Done & Explore More
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useBooking } from '../composables/useBooking'

const {
  isConfirmationModalOpen,
  confirmedReservation,
  closeConfirmationModal,
} = useBooking()

const formatCurrency = (val) => {
  if (!val) return '0'
  return new Intl.NumberFormat('id-ID').format(Math.round(val))
}
</script>

<style scoped>
.modal-scrim {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 210;
  padding: 16px;
}

.confirmation-dialog-card {
  width: 100%;
  max-width: 540px;
  background: var(--colors-canvas);
  border-radius: var(--radius-lg);
  border: 1px solid var(--colors-hairline);
  box-shadow: none;
  overflow: hidden;
  animation: modalPop 0.25s ease-out;
}

@keyframes modalPop {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.celebration-header {
  background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
  padding: 32px 24px 24px;
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.celebration-icon-ring {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: #22c55e;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: none;
  margin-bottom: 16px;
}

.check-icon {
  width: 32px;
  height: 32px;
}

.celebration-title {
  font-size: 22px;
  font-weight: 700;
  color: #14532d;
  margin-bottom: 6px;
}

.celebration-subtitle {
  font-size: 13px;
  color: #166534;
  max-width: 400px;
}

.dialog-body {
  padding: 24px;
}

.reference-code-card {
  background: var(--colors-surface-soft);
  border: 1px dashed var(--colors-border-strong);
  border-radius: var(--radius-sm);
  padding: 14px 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 16px;
}

.ref-label {
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: var(--colors-muted);
}

.ref-code {
  font-size: 18px;
  font-weight: 700;
  color: var(--colors-ink);
  letter-spacing: 1px;
}

.status-confirmed-pill {
  background: #dcfce7;
  color: #15803d;
  font-size: 10px;
  font-weight: 700;
  padding: 3px 8px;
  border-radius: var(--radius-full);
}

.points-awarded-card {
  background: linear-gradient(135deg, #ede9fe 0%, #f5f3ff 100%);
  border: 1px solid #c4b5fd;
  border-radius: var(--radius-md);
  padding: 16px;
  display: flex;
  align-items: center;
  gap: 14px;
  margin-bottom: 16px;
}

.points-awarded-card.is-pending {
  background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
  border-color: #fde68a;
}

.points-awarded-card.is-pending .points-sparkle-badge {
  background: #fef08a;
  color: #b45309;
}

.points-awarded-card.is-pending .points-title {
  color: #92400e;
}

.points-awarded-card.is-pending .points-desc {
  color: #b45309;
}

.points-sparkle-badge {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #ddd6fe;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #6d28d9;
  flex-shrink: 0;
}

.sparkle-svg {
  width: 22px;
  height: 22px;
}

.points-title {
  font-size: 16px;
  font-weight: 700;
  color: #5b21b6;
}

.points-desc {
  font-size: 12px;
  color: #6d28d9;
  margin-top: 2px;
}

.details-summary-card {
  background: var(--colors-canvas);
  border: 1px solid var(--colors-hairline-soft);
  border-radius: var(--radius-sm);
  padding: 16px;
  margin-bottom: 20px;
}

.summary-line {
  display: flex;
  justify-content: space-between;
  font-size: 13px;
  margin-bottom: 8px;
}

.line-label {
  color: var(--colors-muted);
}

.line-value {
  font-weight: 600;
  color: var(--colors-ink);
  text-align: right;
}

.summary-divider {
  height: 1px;
  background: var(--colors-hairline-soft);
  margin: 12px 0;
}

.total-line {
  font-size: 15px;
  margin-bottom: 0;
}

.total-amount {
  font-size: 18px;
  font-weight: 700;
  color: var(--colors-primary);
}

.btn-done {
  width: 100%;
  height: 48px;
}
</style>
