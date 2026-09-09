<template>
  <div v-if="property" class="property-header-section">
    <!-- Title & Meta Header -->
    <div class="property-headline-row">
      <div class="title-with-badge">
        <h1 class="property-title">{{ property.name }}</h1>
        <span v-if="property.has_membership" class="partner-club-badge">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="badge-svg">
            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
          </svg>
          <span>Jeevawasa Member Sanctuary</span>
        </span>
      </div>

      <div class="property-meta-row">
        <div class="meta-rating">
          <svg class="star-icon" viewBox="0 0 24 24" fill="currentColor">
            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
          </svg>
          <span class="rating-number">{{ property.review_score }}</span>
          <span class="review-count">({{ property.review_count }} reviews)</span>
        </div>
        <span class="dot-separator">•</span>
        <span class="badge-guest-favorite">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="heart-icon">
            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
          </svg>
          Guest favorite
        </span>
        <span class="dot-separator">•</span>
        <span class="location-link">{{ property.city }}, {{ property.country }}</span>
      </div>
    </div>

    <!-- Airbnb-Style 5-Photo Mosaic Grid (Zero Drop Shadow, Clean Hairline Framing) -->
    <div class="photo-mosaic-grid">
      <div class="photo-main">
        <img :src="mainPhoto" :alt="property.name" class="mosaic-img" />
      </div>
      <div class="photo-subgrid">
        <div v-for="(photo, idx) in subPhotos" :key="idx" class="photo-cell">
          <img :src="photo" :alt="property.name + ' ' + (idx + 1)" class="mosaic-img" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  property: {
    type: Object,
    required: true
  }
})

const mainPhoto = computed(() => {
  return props.property.image_url || props.property.gallery?.[0]
})

const subPhotos = computed(() => {
  const g = props.property.gallery || []
  const photos = g.slice(1, 5)
  while (photos.length < 4) {
    photos.push(mainPhoto.value)
  }
  return photos
})
</script>

<style scoped>
.property-header-section {
  margin-bottom: 32px;
}

.property-headline-row {
  margin-bottom: 20px;
}

.title-with-badge {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
  margin-bottom: 8px;
}

.property-title {
  font-size: 28px;
  font-weight: 700;
  color: var(--colors-ink);
  letter-spacing: -0.4px;
}

.partner-club-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: var(--colors-surface-soft);
  color: var(--colors-ink);
  border: 1px solid var(--colors-hairline-soft);
  padding: 4px 12px;
  border-radius: var(--radius-full);
  font-size: 12px;
  font-weight: 600;
  letter-spacing: 0.2px;
}

.badge-svg {
  width: 13px;
  height: 13px;
  color: #b45309;
}

.property-meta-row {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 8px;
  font-size: 14px;
}

.meta-rating {
  display: flex;
  align-items: center;
  gap: 4px;
  font-weight: 600;
  color: var(--colors-star-rating);
}

.star-icon {
  width: 14px;
  height: 14px;
}

.review-count {
  color: var(--colors-muted);
  font-weight: 400;
}

.dot-separator {
  color: var(--colors-muted);
}

.heart-icon {
  width: 12px;
  height: 12px;
  color: var(--colors-primary);
}

.location-link {
  font-weight: 500;
  text-decoration: underline;
  color: var(--colors-ink);
}

/* Mosaic Photo Grid (Airbnb Style) */
.photo-mosaic-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px;
  height: 440px;
  border-radius: var(--radius-md);
  overflow: hidden;
  border: 1px solid var(--colors-hairline-soft);
  background: var(--colors-surface-soft);
}

.photo-main {
  height: 100%;
  overflow: hidden;
}

.photo-subgrid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  grid-template-rows: 1fr 1fr;
  gap: 8px;
  height: 100%;
}

.photo-cell {
  overflow: hidden;
  height: 100%;
}

.mosaic-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease, filter 0.2s ease;
  display: block;
}

.mosaic-img:hover {
  transform: scale(1.02);
  filter: brightness(0.96);
}

@media (max-width: 744px) {
  .photo-mosaic-grid {
    grid-template-columns: 1fr;
    height: 280px;
  }
  .photo-subgrid {
    display: none;
  }
}
</style>
