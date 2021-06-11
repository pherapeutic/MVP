import ActionTypes from '../types';

export const login = payload => ({
  type: ActionTypes.USER_LOGIN,
  payload,
});

export const saveUser = payload => ({
  type: ActionTypes.SAVE_USER,
  payload
});

export const saveUserProfile = payload => ({
  type: ActionTypes.SAVE_USER_PROFILE,
  payload
})
