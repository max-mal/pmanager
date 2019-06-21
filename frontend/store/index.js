export const state = () => ({
  masterPassword: null,
})

export const mutations = {
  setMasterPassword (state, password) {
    state.masterPassword = password
  }
}