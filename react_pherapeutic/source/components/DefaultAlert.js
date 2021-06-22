import {Alert, Dimensions, Platform} from 'react-native';

export const defaultAlert = (
  message = '',
  buttons = [{text: 'Cancel'}, {text: 'Confirm'}],
) => {
  Alert.alert('PheraPeutic', message, buttons);
};

export const isIphoneX = () => {
  const dimen = Dimensions.get('window');
  return (
    Platform.OS === 'ios' &&
    !Platform.isPad &&
    !Platform.isTVOS &&
    (dimen.height === 812 ||
      dimen.width === 812 ||
      dimen.height === 896 ||
      dimen.width === 896)
  );
};
