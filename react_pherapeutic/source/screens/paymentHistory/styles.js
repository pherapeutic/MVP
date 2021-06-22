import { Dimensions, StyleSheet } from 'react-native';
import constants from '../../utils/constants';

const { height, width } = Dimensions.get('window');

export default StyleSheet.create({
  container: {
    flex: 1,
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
  header: {
    flex: 1.5,
    justifyContent: 'center',
    alignItems: 'center',
    flexDirection: 'row'
  },
  content: {
    flex: 8.8,
    justifyContent: 'flex-start',
    alignItems: 'center',
  },
  cardView: {
    backgroundColor: constants.colors.white,
    justifyContent: 'center',
    alignItems: 'flex-start',
    borderRadius: 10,
    flexDirection: "row",
    padding: height * 0.013,
    width: width * 0.9,
    height: height * 0.10,
    marginTop:height * 0.02
  },
  wrap: {
    flex: 1,
    justifyContent: 'flex-start',
    alignItems: 'center',
    flexDirection: 'row',


  },
  imageView: {
    flex: 2,
    justifyContent: "center"

  },
  imageSize: {
    height: 60,
    width: 60
  },

  contentView: {
    flex: 5,


  },
  paidView: {
    flex: 3,


  },
  imageStyle: {
    height: 60,
    width: 60,
    backgroundColor: "red"
  },
  starStyle: {
    alignSelf:"flex-start",
   },

})