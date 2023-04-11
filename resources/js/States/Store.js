import { createStore } from 'vuex'

export const store = createStore({
  state: {
    showModal: false,
    idModal: '',
    action: '',
    item: {},
    q: '',
  },
  mutations: {
    toggleModal (state,idModal) {
      state.idModal = idModal ? idModal : ''
      state.showModal = !state.showModal
    },
    hideModal (state) {
      state.showModal = false;
    },
    setAction(state,action) {
      state.action = action ? action : ''
    },
    setPage(state,page) {
      state.page = page ? page : '1'
    },
    setItem(state,item) {
      state.item = item ? item : {}  
    },
    setQuery(state,q) {
      state.q = q ? q : {}  
    }
  },
  getters: {
    showModal: state => {
      return state.showModal
    },
    getAction: state => {
      return state.action; 
    },
    capitalizeAction: state => {
      return state.action[0].toUpperCase() + state.action.substring(1);
    },
    getPage: state => {
      return state.page; 
    },
    getItem: state => {
      return state.item
    },
    getQuery: state => {
      return state.q
    }
  }
});