import { ref, computed } from 'vue'

const token = ref(localStorage.getItem('poc_auth_token') || null)
const memberProfile = ref(JSON.parse(localStorage.getItem('poc_member_profile') || 'null'))
const propertyContext = ref(null)
const isLoading = ref(false)
const authError = ref('')
const isAuthModalOpen = ref(false)
const authModalMode = ref('signin') // 'signin' | 'register'
const signInSubTab = ref('otp') // 'otp' | 'google' | 'password'

export function useAuth() {
  const bffUrl = import.meta.env.VITE_BFF_API_URL || 'http://localhost:8002'

  const isLoggedIn = computed(() => !!token.value && !!memberProfile.value)
  const isAuthenticated = isLoggedIn
  const member = memberProfile

  const memberTier = computed(() => {
    return memberProfile.value?.tier?.name || 'Diamond'
  })

  const memberPoints = computed(() => {
    return memberProfile.value?.member?.points ?? 0
  })

  const memberName = computed(() => {
    return memberProfile.value?.name || 'Valued Member'
  })

  const memberEmail = computed(() => {
    return memberProfile.value?.email || ''
  })

  const memberId = computed(() => {
    return memberProfile.value?.member?.id || null
  })

  const pointPlan = computed(() => {
    return propertyContext.value?.point_plan || null
  })

  const calculatePoints = (amount) => {
    const plan = pointPlan.value
    if (plan && Number(plan.price) > 0 && Number(plan.point) > 0) {
      return Math.floor((Number(amount) / Number(plan.price)) * Number(plan.point))
    }
    // Standard baseline fallback: 1 point per IDR 500,000 spend
    return Math.floor(Number(amount) / 500000)
  }

  const setAuthSession = (data) => {
    if (data.access_token) {
      token.value = data.access_token
      localStorage.setItem('poc_auth_token', data.access_token)
    }

    if (data.member_profile) {
      // Default tier to Diamond if unassigned in test DB
      if (!data.member_profile.tier || !data.member_profile.tier.name || data.member_profile.tier.name === '-') {
        data.member_profile.tier = {
          id: 'tier_diamond',
          name: 'Diamond',
          discount: 20,
        }
      }
      memberProfile.value = data.member_profile
      localStorage.setItem('poc_member_profile', JSON.stringify(data.member_profile))
    }
  }

  const clearAuthSession = () => {
    token.value = null
    memberProfile.value = null
    localStorage.removeItem('poc_auth_token')
    localStorage.removeItem('poc_member_profile')
  }

  const fetchPropertyContext = async (propertyId) => {
    try {
      const res = await fetch(`${bffUrl}/api/auth/property-context?property_id=${propertyId}`)
      const json = await res.json()
      if (json.status && json.data) {
        propertyContext.value = json.data
      }
    } catch (err) {
      console.warn('Property context discovery unavailable:', err.message)
    }
  }

  const requestOtp = async (propertyId, email) => {
    isLoading.value = true
    authError.value = ''
    try {
      const response = await fetch(`${bffUrl}/api/auth/otp/request`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          property_id: propertyId,
          email: email.trim(),
        }),
      })

      const data = await response.json()
      if (!response.ok || !data.status) {
        throw new Error(data.message || 'Failed to request OTP code.')
      }

      return data
    } catch (err) {
      authError.value = err.message
      throw err
    } finally {
      isLoading.value = false
    }
  }

  const verifyOtp = async (propertyId, email, otp) => {
    isLoading.value = true
    authError.value = ''
    try {
      const response = await fetch(`${bffUrl}/api/auth/otp/verify`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          property_id: propertyId,
          email: email.trim(),
          otp: otp.trim(),
        }),
      })

      const data = await response.json()
      if (!response.ok || !data.status) {
        throw new Error(data.message || 'Failed to verify OTP code.')
      }

      setAuthSession(data.data)
      isAuthModalOpen.value = false
      return data.data
    } catch (err) {
      authError.value = err.message
      throw err
    } finally {
      isLoading.value = false
    }
  }

  const loginWithGoogle = async (propertyId, credentialOrEmail = 'madewedaoffice@gmail.com') => {
    isLoading.value = true
    authError.value = ''
    try {
      // Determine whether credentialOrEmail is a real Google JWT ID token or mock email
      const isRealJwt = typeof credentialOrEmail === 'string' && credentialOrEmail.includes('.') && credentialOrEmail.length > 50
      const idToken = isRealJwt ? credentialOrEmail : `mock_google_${credentialOrEmail.trim()}`

      const response = await fetch(`${bffUrl}/api/auth/google`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          property_id: propertyId,
          id_token: idToken,
        }),
      })

      const data = await response.json()
      if (!response.ok || !data.status) {
        throw new Error(data.message || 'Google authentication failed.')
      }

      setAuthSession(data.data)
      isAuthModalOpen.value = false
      return data.data
    } catch (err) {
      authError.value = err.message
      throw err
    } finally {
      isLoading.value = false
    }
  }

  const loginWithPassword = async (propertyId, email, password) => {
    isLoading.value = true
    authError.value = ''
    try {
      const response = await fetch(`${bffUrl}/api/auth/login`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          property_id: propertyId,
          email: email.trim(),
          password: password,
        }),
      })

      const data = await response.json()
      if (!response.ok || !data.status) {
        throw new Error(data.message || 'Invalid email or password.')
      }

      setAuthSession(data.data)
      isAuthModalOpen.value = false
      return data.data
    } catch (err) {
      authError.value = err.message
      throw err
    } finally {
      isLoading.value = false
    }
  }

  const requestRegisterOtp = async (propertyId, email) => {
    isLoading.value = true
    authError.value = ''
    try {
      const res = await fetch(`${bffUrl}/api/auth/register/otp/request`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          property_id: propertyId,
          email: email.trim(),
        }),
      })

      const data = await res.json()
      if (!res.ok || !data.status) {
        throw new Error(data.message || 'Failed to dispatch registration OTP.')
      }

      return data
    } catch (err) {
      authError.value = err.message
      throw err
    } finally {
      isLoading.value = false
    }
  }

  const verifyRegisterOtp = async (propertyId, email, otp) => {
    isLoading.value = true
    authError.value = ''
    try {
      const res = await fetch(`${bffUrl}/api/auth/register/otp/verify`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          property_id: propertyId,
          email: email.trim(),
          otp: otp.trim(),
        }),
      })

      const data = await res.json()
      if (!res.ok || !data.status) {
        throw new Error(data.message || 'Invalid registration OTP code.')
      }

      return data.data // returns { registration_token }
    } catch (err) {
      authError.value = err.message
      throw err
    } finally {
      isLoading.value = false
    }
  }

  const registerMember = async (propertyId, registrationData) => {
    isLoading.value = true
    authError.value = ''
    try {
      const res = await fetch(`${bffUrl}/api/auth/register`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          property_id: propertyId,
          ...registrationData,
        }),
      })

      const data = await res.json()
      if (!res.ok || !data.status) {
        throw new Error(data.message || 'Registration failed.')
      }

      setAuthSession(data.data)
      isAuthModalOpen.value = false
      return data.data
    } catch (err) {
      authError.value = err.message
      throw err
    } finally {
      isLoading.value = false
    }
  }

  const logout = async (propertyId) => {
    if (token.value && propertyId) {
      try {
        await fetch(`${bffUrl}/api/auth/logout`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': `Bearer ${token.value}`,
          },
          body: JSON.stringify({
            property_id: propertyId,
          }),
        })
      } catch (err) {
        // Suppress network error during logout cleanup
      }
    }

    clearAuthSession()
  }

  const updatePoints = (newPoints) => {
    if (memberProfile.value?.member) {
      memberProfile.value.member.points = newPoints
      localStorage.setItem('poc_member_profile', JSON.stringify(memberProfile.value))
    }
  }

  const fetchMemberTransactions = async (propertyId, page = 1, limit = 5) => {
    if (!token.value) {
      return { transactions: [], meta: { page, limit, total: 0 } }
    }
    try {
      const res = await fetch(`${bffUrl}/api/auth/transactions?property_id=${propertyId}&page=${page}&limit=${limit}`, {
        headers: {
          'Authorization': `Bearer ${token.value}`,
          'Accept': 'application/json',
        },
      })
      const json = await res.json()
      if (json.status && json.data) {
        return json.data
      }
      return { transactions: [], meta: { page, limit, total: 0 } }
    } catch (err) {
      console.warn('Failed to fetch member transactions:', err.message)
      return { transactions: [], meta: { page, limit, total: 0 } }
    }
  }

  const openAuthModal = (mode = 'signin', subTab = 'otp') => {
    authModalMode.value = mode
    signInSubTab.value = subTab
    authError.value = ''
    isAuthModalOpen.value = true
  }

  const closeAuthModal = () => {
    isAuthModalOpen.value = false
    authError.value = ''
  }

  return {
    token,
    memberProfile,
    propertyContext,
    pointPlan,
    calculatePoints,
    isLoggedIn,
    isAuthenticated,
    member,
    memberTier,
    memberPoints,
    memberName,
    memberEmail,
    memberId,
    isLoading,
    authError,
    isAuthModalOpen,
    authModalMode,
    signInSubTab,
    openAuthModal,
    closeAuthModal,
    fetchPropertyContext,
    requestOtp,
    verifyOtp,
    loginWithGoogle,
    loginWithPassword,
    requestRegisterOtp,
    verifyRegisterOtp,
    registerMember,
    logout,
    updatePoints,
    fetchMemberTransactions,
  }
}
