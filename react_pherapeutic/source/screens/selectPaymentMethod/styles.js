import {Dimensions, StyleSheet} from 'react-native';
import constants from '../../utils/constants';

const {height, width} = Dimensions.get('window');

export default StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  containerBackground: {
    height: '100%',
    width,
    position: 'absolute',
    top: 0,
    left: 0,
    bottom: 0,
    right: 0,
    zIndex: -2,
  },
  //   card: {
  //     flex: 6,
  //     justifyContent: 'flex-start',
  //     alignItems: 'center',
  //     backgroundColor: constants.colors.white,
  //     width: '80%',
  //     height: height * 0.02,
  //   },
});
