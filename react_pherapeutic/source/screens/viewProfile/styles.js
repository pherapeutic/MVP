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
    top: -(height * 0.06),
    left: 0,
    right: 0,
    zIndex: -1
  },
  backButtonView: {
    flex: 1.5,
    // paddingHorizontal: width * 0.05,
    width,
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center'
  },
  logoView: {
    flex: 1.5,
    justifyContent: 'center',
    alignItems: 'center'
  },
  formView: {
    flex: 8.5,
    justifyContent: 'flex-start',
    alignItems: 'center'
  },
  footerView: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  footerText: {
    fontSize: 12,
    color: constants.colors.white,
    fontWeight: '500'
  },
  footerlinkText: {
    fontSize: 12,
    color: constants.colors.link,
    fontWeight: '500'
  },
  formWrap: {
    width: width * 0.88,
    justifyContent: 'center',
    alignItems: 'center',
    paddingTop: height * 0.018,
    paddingBottom: height * 0.015,
    backgroundColor: constants.colors.formBackground,
    borderRadius: 10,
  },
  formField: {
//    height: height * 0.075,
    width: width * 0.8,
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: height * 0.016
  },
  fieldName: {
    fontSize: 12,
    color: constants.colors.fieldName,
    fontWeight: '400'
  },
  fieldInputWrap: {
    height: height * 0.048,
    width: width * 0.8,
    backgroundColor: constants.colors.white,
    borderRadius: 4,
    justifyContent: 'center',
    alignItems: 'center',
  },
  rememberMeView: {
    width: width * 0.84,
    justifyContent: 'center',
    alignItems: 'center'
  },
  fieldInput: {
    color: '#939393',
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
  },
  profileImage: {
    height: height * 0.12,
    width: height * 0.12,
    borderRadius: 10
  },
  header: {
    flex: 1.5,
    justifyContent: 'center',
    alignItems: 'center',
    flexDirection: 'row'
  },
  backButtonView: {
    flex: 1.2,
    width,
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center'
  },
  headingView: {
    flex: 4,
    justifyContent: 'center',
    alignItems: 'center',
  },
  headingText: {
    fontSize: 20,
    color: constants.colors.greenText,
    fontWeight: '500'
  },
})