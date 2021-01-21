import { Dimensions, StyleSheet } from 'react-native';
import constants from '../../utils/constants';

const { height, width } = Dimensions.get('window');

export default StyleSheet.create({
  container: {
    // flex: 1,
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
    top: -(height * 0.06),
    left: 0,
    right: 0,
    zIndex: -1
  },
  logoView: {
    flex: 1.5,
    justifyContent: 'center',
    alignItems: 'center'
  },
  backButtonView: {
    // flex: 1.5,
    height: height * 0.15,
    width,
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center'
  },
  formView: {
    // flex: 7.5,
    // height: height * 0.75,
    width,
    justifyContent: 'flex-start',
    alignItems: 'center',
    //paddingTop: height * 0.01
  },
  footerView: {
    // flex: 1,
   // height: height * 0.1,
    width,
    justifyContent: 'space-around',
    alignItems: 'center',
    paddingBottom: 10,
    paddingBottom: 6
  },
  footerText: {
    fontSize: 13,
    color: constants.colors.white,
    fontWeight: '500'
  },
  footerlinkText: {
    fontSize: 13,
    color: constants.colors.greenText,
    fontWeight: '500'
  },
  footerLinkTextBottom: {
    fontSize: 12,
    color: constants.colors.white,
    fontWeight: '500'
  },
  formWrap: {
    width: width * 0.88,
    justifyContent: 'center',
    alignItems: 'center',
    paddingBottom: height * 0.017,
    paddingTop: height * 0.01,
    backgroundColor: constants.colors.formBackground,
    borderRadius: 10,
  },
  formField: {
    height: height * 0.075,
    width: width * 0.88,
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    paddingHorizontal: width * 0.04,
    marginBottom: height * 0.015,
  },
  fieldName: {
    fontSize: 12,
    color: constants.colors.fieldName,
    fontWeight: '500'
  },
  fieldInputWrap: {
    height: height * 0.049,
    width: width * 0.8,
    backgroundColor: constants.colors.white,
    borderRadius: 4,
    justifyContent: 'center',
    alignItems: 'center',
    flexDirection: 'row'
  },
  rememberMeView: {
    width: width * 0.84,
    justifyContent: 'center',
    alignItems: 'center'
  },
  fieldInput: {
    height: height * 0.049,
    width: width * 0.8,
    fontSize: 14,
    fontWeight: '500',
    paddingLeft: 10,
    padding:5
  },
  forgotPasswordView: {
    width: width * 0.84,
    justifyContent: 'center',
    alignItems: 'center'
  },
  forgotPasswordText: {
    fontSize: 11,
    color: constants.colors.purpleText,
    fontWeight: '400'
  }
})