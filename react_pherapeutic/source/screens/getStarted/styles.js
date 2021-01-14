import { Dimensions, StyleSheet } from 'react-native';
import constants from '../../utils/constants';

const { height, width } = Dimensions.get('window');

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
    zIndex: -2
  },
  backButtonView: {
    flex: 1.5,
    paddingHorizontal: width * 0.05,
    width,
    justifyContent: 'center',
    alignItems: 'flex-end'
  },
  middleView: {
    flex: 6.5,
    justifyContent: 'center',
    alignItems: 'center',
    width
  },
  footerView: {
    flex: 2,
    justifyContent: 'center',
    alignItems: 'center',
  },
  footerSubview: {
    flex: 1,
    justifyContent: 'flex-start',
    alignItems: 'center',
  },
  footerText: {
    fontSize: 13, 
    color: constants.colors.white,
    fontWeight: '500'
  },
  footerlinkText: {
    fontSize: 12,
    color: constants.colors.greenText,
    fontWeight: '500'
  },
});