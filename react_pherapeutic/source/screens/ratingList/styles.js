import { Dimensions, StyleSheet } from 'react-native';
import constants from '../../utils/constants';

const { height, width } = Dimensions.get('window');

export default StyleSheet.create({
    container: {
        flex: 1,
       justifyContent: 'flex-start',
        alignItems: 'center',
    },
    content: {
        flex: 8.8,
        marginTop:20,
        marginBottom:50,
        justifyContent: 'flex-start',
        alignItems: 'center',
      },
    starStyle: {
     alignSelf:"flex-start",
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
    cardStyle:{
       backgroundColor: constants.colors.white,
        width: width * 0.9,
        height:height * 0.45,
        borderRadius: 10,
       justifyContent:"flex-start",
    },
    imageView:{
        // width: '75%',
        // height:100,
        height:100,
        width:100,
        marginLeft:"5%",
        marginTop:"10%",
        borderWidth:1,
        borderRadius:20,
        borderColor:"black",
        alignSelf:"flex-start"
    },
    profileImageWrap: {
        height: height * 0.09,
        width: height * 0.09,
        borderRadius: 7
      },
      profileDetailView: {
        height: height * 0.09,
        marginLeft: width * 0.05,
        paddingVertical: height * 0.01,
        justifyContent: 'space-between',
        alignItems: 'flex-start',
    
      },
    reviewTextHeader1: {
      
        fontSize: 20,
        paddingLeft: 13,
        color: constants.colors.placeholder,
        fontWeight: '500',
        paddingBottom:"10%"
    },
  
   
})