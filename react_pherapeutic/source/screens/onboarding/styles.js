import { Dimensions, StyleSheet } from 'react-native';
import constants from '../../utils/constants';

const { height, width } = Dimensions.get('window');

export default styles = StyleSheet.create({
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
  headingText: {
    color: '#ffffff',
    fontSize: 23,
    fontWeight: '500',
    lineHeight: 30,
    letterSpacing:1
  },
  contentText: {
    color: '#ffffff',
    fontSize: 16,
    fontWeight: '400',
    lineHeight: 20,
    letterSpacing:1
  },
  headingWrap:{
    justifyContent: 'center',
     alignItems: 'center',
     width,
     paddingVertical: height * 0.03
  },
  backButtonView: {
    height: height * 0.15,
    paddingHorizontal: width * 0.05,
    width,
    justifyContent: 'center',
    alignItems: 'flex-end'
  },
})