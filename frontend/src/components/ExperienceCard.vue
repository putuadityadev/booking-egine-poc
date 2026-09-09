<template>
  <div class="experience-card">
    <div class="photo-plate">
      <img :src="experience.image_url" :alt="experience.name" class="experience-img" />
      <div class="photo-overlay">
        <span class="badge-new">NEW</span>
        <button
          class="heart-btn"
          :class="{ active: isLiked }"
          @click.stop="isLiked = !isLiked"
        >
          <svg viewBox="0 0 24 24" fill="currentColor" class="heart-svg">
            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
          </svg>
        </button>
      </div>
    </div>

    <div class="experience-meta">
      <div class="rating-duration-row">
        <div class="rating-box">
          <svg class="star-icon" viewBox="0 0 24 24" fill="currentColor">
            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
          </svg>
          <span class="rating-val">4.98</span>
        </div>
        <span>•</span>
        <span class="duration-text">{{ experience.duration_hours }} Hours</span>
        <span>•</span>
        <span class="category-text">{{ experience.category }}</span>
      </div>

      <h4 class="exp-title">{{ experience.name }}</h4>
      <p class="exp-desc">{{ experience.description }}</p>

      <div class="member-perk-notice">
        ⚡ {{ experience.member_perk || 'Club Members earn 2x points on experiences' }}
      </div>

      <div class="exp-price-row">
        <div>
          <span class="exp-price">IDR {{ formatCurrency(experience.price) }}</span>
          <span class="exp-unit"> / experience</span>
        </div>
        <button class="btn-secondary btn-book-exp" @click="handleBookExperience">
          Inquire
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  experience: {
    type: Object,
    required: true
  }
})

const isLiked = ref(false)

const formatCurrency = (val) => {
  if (isNaN(val)) return '0'
  return new Intl.NumberFormat('id-ID').format(Math.round(val))
}

const handleBookExperience = () => {
  alert(`Experience booking inquiry for "${props.experience.name}" is logged! Member concierge will assist upon check-in.`)
}
</script>

<style scoped>
.experience-card {
  background: var(--colors-canvas);
  border-radius: 16px;
  border: 1px solid var(--colors-hairline-soft);
  overflow: hidden;
  box-shadow: none;
  display: flex;
  flex-direction: column;
  transition: border-color 0.2s ease;
}

.experience-card:hover {
  border-color: var(--colors-ink);
}

.photo-plate {
  position: relative;
  width: 100%;
  aspect-ratio: 4 / 3;
  overflow: hidden;
  background-color: var(--colors-surface-soft);
}

.experience-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.experience-card:hover .experience-img {
  transform: scale(1.04);
}

.photo-overlay {
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
  transition: transform 0.15s ease;
}

.heart-btn.active {
  background: var(--colors-canvas);
  color: var(--colors-primary);
}

.heart-svg {
  width: 16px;
  height: 16px;
}

.experience-meta {
  padding: 16px;
  display: flex;
  flex-direction: column;
  flex: 1;
}

.rating-duration-row {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  color: var(--colors-muted);
  margin-bottom: 6px;
}

.rating-box {
  display: flex;
  align-items: center;
  gap: 3px;
  font-weight: 600;
  color: var(--colors-star-rating);
}

.star-icon {
  width: 12px;
  height: 12px;
}

.exp-title {
  font-size: 16px;
  font-weight: 700;
  color: var(--colors-ink);
  margin-bottom: 6px;
}

.exp-desc {
  font-size: 13px;
  color: var(--colors-body);
  line-height: 1.4;
  margin-bottom: 12px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.member-perk-notice {
  font-size: 11px;
  font-weight: 600;
  color: #065f46;
  background: #d1fae5;
  padding: 4px 8px;
  border-radius: 4px;
  margin-bottom: 14px;
}

.exp-price-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: auto;
  padding-top: 10px;
  border-top: 1px solid var(--colors-hairline-soft);
}

.exp-price {
  font-size: 16px;
  font-weight: 700;
  color: var(--colors-ink);
}

.exp-unit {
  font-size: 12px;
  color: var(--colors-muted);
}

.btn-book-exp {
  padding: 6px 14px;
  font-size: 13px;
}
</style>
