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
  formsBackground: {
    width,
    position: 'absolute',
    top: -(height * 0.1),
    left: 0,
    right: 0,
    zIndex: -1
  },

  backButtonView: {
    flex: 1.2,
    width,
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center'
  },
  content: {
    flex: 8.8,
    width: width,
    justifyContent: 'flex-start',
    alignItems: 'center'
  },
  heading: {
    height: height * 0.1,
    width: width * 0.8,
    alignItems: 'flex-start',
    justifyContent: 'flex-start',
  },
  formWrap: {
    width: width * 0.88,
    justifyContent: 'center',
    alignItems: 'center',
    paddingBottom: height * 0.017,
    paddingTop: height * 0.02,
    backgroundColor: constants.colors.formBackground,
    borderRadius: 10,
  },
  formField: {
    height: height * 0.05,
    width: width * 0.8,
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
    marginVertical: height * 0.015
  },
  codeInput:{
    height: height * 0.05,
    width: width * 0.12,
    borderRadius: 4,
    borderWidth: 0,
    backgroundColor: '#ffffff',
    justifyContent: 'center',
    alignItems: 'center',
    color: '#000000',
    fontSize: 14
  },
  codeInputContainer:{
    height: height * 0.051,
    width: width * 0.8,
    justifyContent: 'center',
    alignItems: 'center',
    marginVertical: height * 0.025
  },
  headingText: {
    fontSize: 20,
    color: constants.colors.greenText,
    fontWeight: '500'
  },
  topText: {
    fontSize: 16,
    color: "#000000",
    fontWeight: '500'
  }, 
})