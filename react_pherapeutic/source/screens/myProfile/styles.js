import {Dimensions, StyleSheet} from 'react-native';
import constants from '../../utils/constants';
import Scale from '../../utils/Scale';
const {height, width} = Dimensions.get('window');

export default StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: 'flex-start',
    alignItems: 'center',
  },
  header: {
    flex: 1.5,
    justifyContent: 'center',
    alignItems: 'center',
    flexDirection: 'row',
  },
  content: {
    flex: 8.9,

    //justifyContent: 'flex-start',
    justifyContent: 'space-evenly',
    flexDirection: 'column',
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
  backButtonView: {
    flex: 1.2,
    width,
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
  },
  headingView: {
    flex: 4,
    justifyContent: 'center',
    alignItems: 'center',
  },
  headingText: {
    fontSize: 20,
    color: constants.colors.white,
    fontWeight: '500',
  },
  optionsWrap: {
    width: width * 0.9,
    backgroundColor: constants.colors.white,
    justifyContent: 'center',
    alignItems: 'center',
    borderRadius: 10,
    marginVertical: height * 0.018,
  },
  pofileView: {
    backgroundColor: constants.colors.white,

    justifyContent: 'space-evenly',

    borderRadius: 10,

    flexDirection: 'column',
    alignContent: 'center',

    width: width * 0.9,
  },
  profileWrap: {
    //height: Platform.OS == 'android' ? height * 0.09 : height / 9,
    justifyContent: 'space-between',
    alignItems: 'center',
    flexDirection: 'row',

    padding: 5,
  },
  probonoWrap: {
    flexDirection: 'row',
    justifyContent: 'space-evenly',
    alignItems: 'center',
    padding: 2,
    textAlign: 'center',
    width: width / 1.2,

    margin: Scale.moderateScale(10),
    //margin: height * 0.02,
  },
  profileImageWrap: {
    //height: height * 0.09,
    //width: height * 0.09,
    height: Platform.OS == 'android' ? height * 0.09 : height / 8,
    width: Platform.OS == 'android' ? height * 0.09 : height / 8,

    borderRadius: 7,
    // justifyContent: 'flex-start',
    // alignItems: 'center',
  },
  profileDetailView: {
    width: width / 2,
    marginLeft: width * 0.05,
    flex: 2,

    flexDirection: 'column',
    justifyContent: 'space-evenly',
    marginLeft: 10,
    alignItems: 'flex-start',
  },
  profileView: {
    width: width * 0.9,
    backgroundColor: constants.colors.white,
    // justifyContent: 'center',
    justifyContent: 'space-between',
    alignItems: 'center',
    borderRadius: 5,
    marginVertical: height * 0.01,
    padding: height * 0.015,
    flex: 1,
    flexDirection: 'column',
  },
  optionItem: {
    height: height * 0.062,
    width: width * 0.9,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    borderBottomColor: 'rgb(245,245,245)',
    borderBottomWidth: 0.5,
  },
  optionNameView: {
    flex: 9,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'flex-start',
    paddingLeft: width * 0.05,
  },
  nextArrowWrap: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  itemNameText: {
    fontSize: 15,
    fontWeight: '500',
    textAlign: 'center',

    color: constants.colors.fieldName,
  },
  userNameText: {
    fontSize: 16,
    fontWeight: '500',
    textAlign: 'left',

    color: constants.colors.fieldName,
  },
  optionImage: {
    height: 15,
    width: 15,
    marginRight: width * 0.05,
  },
  buttonText: {
    fontSize: 14,
    fontWeight: '500',
    color: constants.colors.white,
  },
  editButton: {
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: constants.colors.greenText,
    // paddingHorizontal: 25,
    height: Platform.OS == 'android' ? 25 : 35,
    padding: 10,
    //paddingVertical: 4,
    borderRadius: 4,
  },
});
