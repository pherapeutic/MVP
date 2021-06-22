import { Dimensions, StyleSheet } from 'react-native';
import { color } from 'react-native-reanimated';
import constants from '../../utils/constants';
import colors from '../../utils/constants/colors';

const { height, width } = Dimensions.get('window');

export default StyleSheet.create({
    container: {
        flex: 1,
    },
    containerBackground: {
        height: '100%',
        width,
        top: 0,
        bottom: 0,
        left: 0,
        right: 0,
        position: 'absolute',
        zIndex: -2
    },
    containerView: {
        flex: 1,
        padding: 20,
    },
    header: {
        flex: 1,
        flexDirection: "row",
        justifyContent: 'space-between',
        alignItems: 'center',
    },
    headingText: {
        fontSize: 20,
        color: constants.colors.white,
        fontWeight: '500'
    },
    LocationInput: {
        flex: 1,
    },
    locationInputBox: {
        flexDirection: 'row',
        borderRadius: 10,
        height: height * 0.059,
        backgroundColor: constants.colors.white,
    },
    mapScreen: {
        flex: 7,
        borderRadius: 10,
        backgroundColor: '#fff',
        marginBottom: 15
    },
    mapView: {
        flex: 8,
        height: 356,
        borderTopLeftRadius: 10,
        borderTopRightRadius: 10,
        // borderTop
        // width:width*0.8,
        paddingHorizontal: 20,
        // top: 20,
        // backgroundColor: constants.colors.white,
        // flex:3
    },
    swipeAction: {
        flex: 1,
        flexDirection: 'row',
        justifyContent: 'center',
        alignItems: 'center'
        // backgroundColor:'red'
    },
    swipeTextProps: {
        color: '#18adbd',
        fontSize: 16,
        marginLeft: 10,
        width: 242,
        height: 23,
    },
    therapistInfo: {
        padding: 20,
        flex: 4,
        flexDirection: "row",
        justifyContent: 'space-around',
        alignItems: 'flex-start',
    },
    name: {
        flexDirection: "row",
        justifyContent: 'flex-end',
        alignItems: 'center',
        fontSize: 14
    },
    price: {
        height: 16,
        fontSize: 14,
        fontWeight: "normal",
        fontStyle: "normal",
        lineHeight: 21,
        color: "#01d8fb"
    },

    callButton: {
        flex: 2,
    },
    misc: {
        padding: 20,
        flex: 1,
        flexDirection: 'row',
        justifyContent: 'space-between',
    },
    textStylesOne: {
        textDecorationLine: 'underline',
        color: '#18adbd',
        fontSize: 16
    },
    textStylesTwo: {
        textDecorationLine: 'underline',
        color: '#18adbd',
        fontSize: 16
    },
    mapContainer: {
        ...StyleSheet.absoluteFillObject,
        height: 400,
        width: 400,
        justifyContent: 'flex-end',
        alignItems: 'center',
    },
    map: {
        ...StyleSheet.absoluteFillObject,
    },

    info:{
        flex:2,
        padding:20,
        justifyContent:'space-between'
    },
    infoDetails:{
        flexDirection:'row',
        justifyContent:'flex-start',
        alignItems:'flex-start'
    },  
    infoHeading:{
        fontWeight:"bold",
        fontSize:16
    },
    infoDesc:{
        fontStyle:'normal',
        fontSize:16
    }
})