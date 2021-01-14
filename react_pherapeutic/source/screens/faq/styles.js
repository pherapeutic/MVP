import { Dimensions, StyleSheet } from 'react-native';
import constants from '../../utils/constants';

const { height, width } = Dimensions.get('window');

export default StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  content: {
    flex: 8.8,
    justifyContent: 'flex-start',
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
    fontSize: 20,
    color: constants.colors.white,
    fontWeight: '500'
  },
  formsBackground: {
    width,
    position: 'absolute',
    top: -(height * 0.08),
    left: 0,
    right: 0,
    zIndex: -1
  },
  backButtonView: {
    flex: 1.2,
    paddingHorizontal: width * 0.05,
    width,
    justifyContent: 'center',
    alignItems: 'center',
    flexDirection: 'row'
  },
  logoView: {
    flex: 1.8,
    justifyContent: 'center',
    alignItems: 'center'
  },
  formView: {
    flex: 6,
    justifyContent: 'flex-start',
    alignItems: 'center'
  },
  footerView: {
    flex: 1,
    justifyContent: 'center',
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
  formWrap: {
    width: width * 0.88,
    justifyContent: 'center',
    alignItems: 'center',
    paddingTop: height * 0.025,
    paddingBottom: height * 0.015,
    backgroundColor: constants.colors.formBackground,
    borderRadius: 10,
    paddingHorizontal: 15,
    margin:5,
    flexDirection: 'row',
    justifyContent: 'space-between',
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
    flexDirection: 'row',
  },
  fieldInput: {
    height: height * 0.049,
    width: width * 0.8,
    fontSize: 14,
    fontWeight: '500',
    paddingLeft: 10,
    padding:5,
    // borderRadius: 6
  },
  tic: {
    width: 13,
    height: 9
  },
  ticWrap: {
    width: width * 0.1,
    justifyContent: 'center',
    alignItems: 'center',
    // borderRadius: 6
  },
  rememberMeView: {
    width: width * 0.84,
    justifyContent: 'center',
    alignItems: 'center'
  },
  forgotPasswordView: {
    width: width * 0.84,
    justifyContent: 'center',
    alignItems: 'center',
    paddingVertical: height * 0.01
  },
  forgotPasswordText: {
    fontSize: 13,
    color: constants.colors.greenText,
    fontWeight: '500'
  },
  wrapDropDownHeader: {
    paddingHorizontal: 15,
    paddingTop: 40,
    flexDirection: 'row',
    justifyContent: 'space-between',
  },
})