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
  formsBackground: {
    width,
    position: 'absolute',
    top: -(height * 0.1),
    left: 0,
    right: 0,
    zIndex: -1,
  },

  backButtonView: {
    flex: 1.2,
    width,
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
  },
  content: {
    flex: 8.8,
    width: width,
    justifyContent: 'flex-start',
    alignItems: 'center',
  },
  heading: {
    height: height * 0.1,
    width: width * 0.7,
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
    marginTop: 20,
  },
  formField: {
    height: height * 0.05,
    width: width * 0.8,
    // flexDirection: 'row',
    // justifyContent: 'center',
    // alignItems: 'center',
    marginVertical: height * 0.02,
  },
  formField3: {
    height: height * 0.05,
    width: width * 0.8,
    // flexDirection: 'row',
    // justifyContent: 'center',
    // alignItems: 'center',
    marginVertical: height * 0.02,
    marginTop: 30,
  },
  formField1: {
    height: height * 0.05,
    width: width * 0.4,
    // flexDirection: 'row',
    // justifyContent: 'center',
    // alignItems: 'center',
    marginVertical: height * 0.02,
    marginRight: 20,
  },

  formField2: {
    height: height * 0.05,
    width: width * 0.3,
    // flexDirection: 'row',
    // justifyContent: 'center',
    // alignItems: 'center',
    marginVertical: height * 0.02,
    marginLeft: 20,
  },
  codeInput: {
    height: height * 0.05,
    width: width * 0.12,
    borderRadius: 4,
    borderWidth: 0,
    backgroundColor: '#ffffff',
    justifyContent: 'center',
    alignItems: 'center',
    color: '#000000',
    fontSize: 14,
  },
  codeInputContainer: {
    height: height * 0.051,
    width: width * 0.8,
    justifyContent: 'center',
    alignItems: 'center',
    marginVertical: height * 0.025,
  },
  headingText: {
    fontSize: 20,
    color: constants.colors.greenText,
    fontWeight: '500',
  },
  topText: {
    fontSize: 16,
    color: '#000000',
    fontWeight: '500',
  },
  fieldName: {
    fontSize: 12,
    color: constants.colors.fieldName,
    fontWeight: '500',
  },
  fieldInputWrap: {
    height: height * 0.052,
    width: width * 0.8,
    backgroundColor: constants.colors.white,
    borderRadius: 4,
    marginTop: 5,
    // justifyContent: 'center',
    // alignItems: 'center',
    // flexDirection: 'row',
  },
  fieldInputWrap1: {
    height: height * 0.052,
    width: width * 0.4,
    backgroundColor: constants.colors.white,
    borderRadius: 4,
    marginTop: 5,
    // justifyContent: 'center',
    // alignItems: 'center',
    // flexDirection: 'row',
  },
  fieldInputWrap2: {
    height: height * 0.052,
    width: width * 0.3,
    backgroundColor: constants.colors.white,
    borderRadius: 4,
    marginTop: 5,
    // justifyContent: 'center',
    // alignItems: 'center',
    // flexDirection: 'row',
  },
  fieldInput: {
    height: height * 0.056,
    width: width * 0.8,
    fontSize: 14,
    fontWeight: '500',
    paddingLeft: 10,
    padding:5
    // borderRadius: 6
  },
  fieldInput1: {
    height: height * 0.056,
    width: width * 0.4,
    fontSize: 14,
    fontWeight: '500',
    paddingLeft: 10,
    // borderRadius: 6
  },
  fieldInput2: {
    height: height * 0.056,
    width: width * 0.3,
    fontSize: 14,
    fontWeight: '500',
    paddingLeft: 10,
    // borderRadius: 6
  },
  tic: {
    width: 13,
    height: 9,
  },
  formView: {
    flex: 6,
    justifyContent: 'flex-start',
    alignItems: 'center',
  },
  ticWrap: {
    width: width * 0.1,
    justifyContent: 'center',
    alignItems: 'center',
    // borderRadius: 6
  },
});
