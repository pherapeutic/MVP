import { Dimensions, StyleSheet, StatusBar } from 'react-native';
import constants from '../../utils/constants';
const { height, width } = Dimensions.get('window');


export default StyleSheet.create({
  container: {
    // flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  outerView: { 
    width: '100%', flex: 1, 
    // paddingTop: StatusBar.currentHeight
    paddingTop: height * 0.03,
  },
  containerBackground: {
    height,
    width,
    position: 'absolute',
    top: 0,
    left: 0,
    bottom: 0,
    right: 0,
    zIndex: -2
  },
  backButtonView: {
    paddingHorizontal: width * 0.05,
    width,
    justifyContent: 'center',
    alignItems: 'flex-start',
    flexDirection: 'row',
    paddingTop: height * 0.04,
  },
  backButtonWrap: { 
    justifyContent: 'center',
     alignItems: 'flex-start', 
     flex: 1, 
     height: height * 0.05,
    },
  headingText: {
    fontSize: 20,
    color: constants.colors.white,
    fontWeight: '500'
  },
  questionItemContainer: {
    // minHeight: height * 0.25,
    width,
    paddingHorizontal: width * 0.05,
    marginVertical: height * 0.02,
    justifyContent: 'center',
    alignItems: 'center'
  },
  questionWrap: {
    width: width * 0.9,
    minHeight: height * 0.055,
    borderRadius: height * 0.025,
    paddingHorizontal: width * 0.05,
    paddingVertical: height * 0.015,
    justifyContent: 'center',
    alignItems: 'flex-start',
    backgroundColor: constants.colors.questionBackground,
    marginBottom: height * 0.02
  },
  questionText: {
    color: constants.colors.questionText,
    fontSize: 18,
    fontWeight: '500',
    lineHeight: 25
  },
  answersView: {
    width: width * 0.9,
    justifyContent: 'flex-start',
    flexDirection: 'row',
    flexWrap: 'wrap'
  },
  choiceView:{
    width: width * 0.9,
    justifyContent: 'center',
    alignItems: 'flex-end',
    marginVertical: height * 0.02
  },
  answerWrap: {
    minWidth: width * 0.25,
    height: height * 0.03,
    paddingHorizontal: 10,
    borderRadius: height * 0.015,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: 'transparent',
    borderWidth: 1,
    borderColor: constants.colors.white,
    marginRight: width * 0.03,
    marginBottom: height * 0.02
  },
  answerText: {
    color: constants.colors.white,
    fontSize: 13,
    fontWeight: '500'
  },
  selectedWrap:{
    minWidth: width * 0.25,
    height: height * 0.03,
    paddingHorizontal: 10,
    borderRadius: height * 0.015,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: constants.colors.lightGreen
  },
  selectedText:{
    color: constants.colors.white,
    fontSize: 13,
    fontWeight: '500'
  }
});