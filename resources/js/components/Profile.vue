<template>
  <div class="min-h-screen bg-gray-100">
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <div class="md:grid md:grid-cols-3 md:gap-6">
          <div class="md:col-span-1">
            <div class="px-4 sm:px-0">
              <h3 class="text-lg font-medium leading-6 text-gray-900">Profile Settings</h3>
              <p class="mt-1 text-sm text-gray-600">
                Update your account information and change your password.
              </p>
            </div>
          </div>
          <div class="mt-5 md:mt-0 md:col-span-2">
            <!-- Tab Navigation -->
            <div class="mb-6">
              <nav class="-mb-px flex space-x-8">
                <button
                  @click="activeTab = 'profile'"
                  :class="[
                    'whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm',
                    activeTab === 'profile'
                      ? 'border-indigo-500 text-indigo-600'
                      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                  ]"
                >
                  Profile Information
                </button>
                <button
                  @click="activeTab = 'password'"
                  :class="[
                    'whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm',
                    activeTab === 'password'
                      ? 'border-indigo-500 text-indigo-600'
                      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                  ]"
                >
                  Change Password
                </button>
              </nav>
            </div>

            <!-- Profile Information Tab -->
            <div v-if="activeTab === 'profile'" class="shadow sm:rounded-md sm:overflow-hidden">
              <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                <div>
                  <h3 class="text-lg leading-6 font-medium text-gray-900">Personal Information</h3>
                  <p class="mt-1 max-w-2xl text-sm text-gray-500">Update your personal details here.</p>
                </div>

                <form @submit.prevent="updateProfile" class="space-y-6">
                  <FormField label="Full Name" required :error="errors.name?.[0]">
                    <InputText
                      v-model="profileForm.name"
                      placeholder="Enter your full name"
                      required
                    />
                  </FormField>

                  <FormField label="Email Address" required :error="errors.email?.[0]">
                    <InputText
                      v-model="profileForm.email"
                      type="email"
                      placeholder="Enter your email address"
                      required
                    />
                  </FormField>

                  <FormActions
                    :loading="profileLoading"
                    submit-text="Save Changes"
                    loading-text="Saving..."
                  />
                </form>
              </div>
            </div>

            <!-- Password Change Tab -->
            <div v-if="activeTab === 'password'" class="shadow sm:rounded-md sm:overflow-hidden">
              <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                <div>
                  <h3 class="text-lg leading-6 font-medium text-gray-900">Change Password</h3>
                  <p class="mt-1 max-w-2xl text-sm text-gray-500">Ensure your account is using a secure password.</p>
                </div>

                <form @submit.prevent="changePassword" class="space-y-6">
                  <FormField label="Current Password" required :error="errors.current_password?.[0]">
                    <InputText
                      v-model="passwordForm.current_password"
                      type="password"
                      placeholder="Enter your current password"
                      required
                    />
                  </FormField>

                  <FormField label="New Password" required :error="errors.password?.[0]">
                    <InputText
                      v-model="passwordForm.password"
                      type="password"
                      placeholder="Enter your new password"
                      required
                    />
                  </FormField>

                  <FormField label="Confirm New Password" required :error="errors.password_confirmation?.[0]">
                    <InputText
                      v-model="passwordForm.password_confirmation"
                      type="password"
                      placeholder="Confirm your new password"
                      required
                    />
                  </FormField>

                  <FormActions
                    :loading="passwordLoading"
                    submit-text="Change Password"
                    loading-text="Changing..."
                  />
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="js">
import { ref, onMounted } from 'vue'
import axios from 'axios'
import FormField from './Form/FormField.vue'
import FormActions from './Form/FormActions.vue'
import InputText from './Form/InputText.vue'

const activeTab = ref('profile')
const profileLoading = ref(false)
const passwordLoading = ref(false)
const errors = ref({})

const profileForm = ref({
  name: '',
  email: ''
})

const passwordForm = ref({
  current_password: '',
  password: '',
  password_confirmation: ''
})

const fetchUser = async () => {
  try {
    const response = await axios.get('/api/user')
    profileForm.value = {
      name: response.data.name,
      email: response.data.email
    }
  } catch (error) {
    console.error('Failed to fetch user:', error)
  }
}

const updateProfile = async () => {
  profileLoading.value = true
  errors.value = {}
  try {
    const response = await axios.put('/api/user/profile', profileForm.value)
    profileForm.value = {
      name: response.data.name,
      email: response.data.email
    }
    if (window.notify) {
      window.notify.success('Success', 'Profile updated successfully')
    }
  } catch (error) {
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    }
    if (window.notify) {
      window.notify.error('Error', 'Failed to update profile')
    }
  } finally {
    profileLoading.value = false
  }
}

const changePassword = async () => {
  passwordLoading.value = true
  errors.value = {}
  try {
    await axios.put('/api/user/password', passwordForm.value)
    passwordForm.value = {
      current_password: '',
      password: '',
      password_confirmation: ''
    }
    if (window.notify) {
      window.notify.success('Success', 'Password changed successfully')
    }
  } catch (error) {
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    }
    if (window.notify) {
      window.notify.error('Error', 'Failed to change password')
    }
  } finally {
    passwordLoading.value = false
  }
}

onMounted(() => {
  fetchUser()
})
</script>
