import ActionTypes from '../types';

const initialState = {
  loginStatus: false,
  userData: {},
  userToken: ''
};

const user = (state = initialState, action) => {
  switch (action.type) {

    case ActionTypes.USER_LOGIN:
      return Object.assign({}, state, {
        loginStatus: true
      });
    case ActionTypes.SAVE_USER:
      return Object.assign({}, state, {
        userData: action.payload,
        userToken: action.payload.auth_token
      });
    case ActionTypes.SAVE_USER_PROFILE:
      return Object.assign({}, state, {
        userData: action.payload
      });
    default:
      return state;
  }
};

export default user;
