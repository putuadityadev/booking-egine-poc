<template>
  <transition name="toast-slide">
    <div
      v-if="isVisible"
      ref="toastRef"
      class="floating-perks-toast"
      :class="{ 'member-active': isLoggedIn }"
    >
      <!-- Guest Mode: 20% Off Room Rates & Reward Points Incentive -->
      <template v-if="!isLoggedIn">
        <div class="toast-icon-wrap">
          <span class="perk-emoji">✨</span>
        </div>

        <div class="toast-content" @click="handleOpenSignIn">
          <div class="toast-title-row">
            <span class="toast-title">Member Privilege</span>
            <span class="toast-tag">20% Off</span>
          </div>
          <p class="toast-desc">
            Unlock exclusive rates & earn reward points on every stay.
          </p>
        </div>

        <div class="toast-actions">
          <button class="btn-toast-signin" @click="handleOpenSignIn">
            Sign In
          </button>
          <button class="btn-toast-dismiss" @click="dismissToast" title="Dismiss">
            ✕
          </button>
        </div>
      </template>

      <!-- Authenticated Member Mode: Compact Status Pill -->
      <template v-else>
        <div class="toast-icon-wrap member-tier-icon">
          <span class="perk-emoji">💎</span>
        </div>

        <div class="toast-content">
          <div class="toast-title-row">
            <span class="toast-title">{{ memberName }}</span>
            <span class="toast-tier-tag">{{ memberTier }}</span>
          </div>
          <p class="toast-desc">
            Member rates active • {{ memberPoints }} Points in wallet.
          </p>
        </div>

        <div class="toast-actions">
          <button class="btn-toast-dismiss" @click="dismissToast" title="Dismiss">
            ✕
          </button>
        </div>
      </template>
    </div>
  </transition>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'
import gsap from 'gsap'
import { useAuth } from '../composables/useAuth'

const { isLoggedIn, memberName, memberTier, memberPoints, openAuthModal } = useAuth()

const toastRef = ref(null)
const isVisible = ref(false)

const DISMISS_KEY = 'stayhub_floating_perks_dismissed'

onMounted(() => {
  const isDismissed = sessionStorage.getItem(DISMISS_KEY)
  if (!isDismissed) {
    // Reveal after brief delay for smooth entrance
    setTimeout(() => {
      isVisible.value = true
      nextTick(() => {
        if (toastRef.value) {
          gsap.fromTo(
            toastRef.value,
            { opacity: 0, y: 20, scale: 0.96 },
            { opacity: 1, y: 0, scale: 1, duration: 0.35, ease: 'power2.out' }
          )
        }
      })
    }, 600)
  }
})

const dismissToast = () => {
  if (toastRef.value) {
    gsap.to(toastRef.value, {
      opacity: 0,
      y: 15,
      scale: 0.95,
      duration: 0.25,
      ease: 'power2.in',
      onComplete: () => {
        isVisible.value = false
        sessionStorage.setItem(DISMISS_KEY, 'true')
      }
    })
  } else {
    isVisible.value = false
    sessionStorage.setItem(DISMISS_KEY, 'true')
  }
}

const handleOpenSignIn = () => {
  openAuthModal('signin')
}
</script>

<style scoped>
.floating-perks-toast {
  position: fixed;
  bottom: 24px;
  left: 24px;
  z-index: 90;
  max-width: 380px;
  width: calc(100vw - 48px);
  background: #ffffff;
  border: 1px solid var(--colors-hairline-soft);
  border-radius: 14px;
  box-shadow: none;
  padding: 12px 14px;
  display: flex;
  align-items: center;
  gap: 12px;
  transition: border-color 0.2s ease;
}

.floating-perks-toast:hover {
  border-color: var(--colors-ink);
}

.toast-icon-wrap {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: #fff1f2;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.toast-icon-wrap.member-tier-icon {
  background: #f5f3ff;
}

.perk-emoji {
  font-size: 16px;
}

.toast-content {
  flex: 1;
  min-width: 0;
  cursor: pointer;
}

.toast-title-row {
  display: flex;
  align-items: center;
  gap: 6px;
  margin-bottom: 2px;
}

.toast-title {
  font-size: 13px;
  font-weight: 700;
  color: var(--colors-ink);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.toast-tag {
  font-size: 10px;
  font-weight: 700;
  color: #ff385c;
  background: #fff1f2;
  padding: 1px 6px;
  border-radius: 4px;
  letter-spacing: 0.3px;
  text-transform: uppercase;
}

.toast-tier-tag {
  font-size: 10px;
  font-weight: 700;
  color: #6d28d9;
  background: #ede9fe;
  padding: 1px 6px;
  border-radius: 4px;
}

.toast-desc {
  font-size: 11px;
  color: var(--colors-muted);
  line-height: 1.35;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.toast-actions {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-shrink: 0;
}

.btn-toast-signin {
  background: transparent;
  border: none;
  color: var(--colors-primary);
  font-size: 12px;
  font-weight: 700;
  text-decoration: underline;
  cursor: pointer;
  padding: 4px 6px;
}

.btn-toast-signin:hover {
  color: var(--colors-primary-active);
}

.btn-toast-dismiss {
  width: 22px;
  height: 22px;
  border-radius: 50%;
  border: 1px solid var(--colors-hairline-soft);
  background: transparent;
  color: var(--colors-muted);
  font-size: 11px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.15s ease;
}

.btn-toast-dismiss:hover {
  color: var(--colors-ink);
  border-color: var(--colors-ink);
  background: var(--colors-surface-soft);
}

@media (max-width: 640px) {
  .floating-perks-toast {
    bottom: 16px;
    left: 16px;
    width: calc(100vw - 32px);
  }
}
</style>
