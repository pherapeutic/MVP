import { Dimensions, StyleSheet } from 'react-native';
import { round } from 'react-native-reanimated';
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
  max: {
    flex: 1,
 
  },
  buttonHolder: {
    zIndex:9999999,
    bottom:20,
    height: 100,
    alignItems: 'center',
    flex: 1,
    flexDirection: 'row',
    justifyContent: 'space-evenly',
  

  },
  button: {
  //  paddingHorizontal: 20,
  //  paddingVertical: 10,
    backgroundColor: 'white',
    borderRadius: 100,
  },
 Endbutton: {
    //  paddingHorizontal: 20,
    //  paddingVertical: 10,
      backgroundColor: '#cc0606',
      borderRadius: 100,
    },
  buttonText: {
    color: '#fff',
  },
  fullView: {
    width: width,
   // height: height - 100,
    height: height ,
  },
  remoteContainer: {
    width: '100%',
    height: 150,
    position: 'absolute',
    top: 5
  },
  remote: {
    width: 150,
    height: 150,
    marginHorizontal: 2.5
  },
  noUserText: {
    paddingHorizontal: 10,
    paddingVertical: 5,
    color: '#0093E9',
  },
})