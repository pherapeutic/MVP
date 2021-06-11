import { Dimensions, StyleSheet } from 'react-native';
import constants from '../../utils/constants';

const { height, width } = Dimensions.get('window');

export default StyleSheet.create({
    container: {
        flex: 1,
        justifyContent: 'center',
        alignItems: 'center',
    },
    starStyle: {
        alignContent: "center"
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
    leaveNoteView: {
        justifyContent: 'center',
        alignItems: 'center',

        height: '40%',
        width: '100%'
    },
    viewNoteField: {
        height: '50%',
        width: '90%',
      //backgroundColor: constants.colors.background,

    },
    fieldName: {
        fontSize: 12,
        color: constants.colors.fieldName,
        backgroundColor: "white",
        fontWeight: '500'
    },

    fieldInputWrap: {
        height: 60,
        width: '100%',
        borderRadius: 4,
        justifyContent: 'center',
        alignItems: 'center',
        flexDirection: 'row',
        backgroundColor: constants.colors.background
    },
    fieldInput: {
        height: '100%',
        width: '100%',
        fontSize: 14,
        fontWeight: '500',
        backgroundColor: "white",
        borderRadius: 6,
        paddingLeft: 10
    },
    leaveNoteWrap: {
        width: '90%',
        height: '90%',
        justifyContent: 'center',
        alignItems: 'center',
        borderRadius: 10,
        backgroundColor: constants.colors.background,
    },
    leaveNoteTextHeader: {
        paddingTop: 10,
        paddingBottom:40,
        fontSize: 20,
        paddingLeft: 13,
        color: constants.colors.placeholder,
        fontWeight: '500',
    },
 
    viewStyle: {
        marginTop: height * 0.10,
        padding:5
    },
    textStyle: {
        textAlign: "left",
        fontSize: 14,
        alignSelf: "flex-start",
        paddingLeft: 20,
        paddingBottom:20,
        color: constants.colors.fieldName,
        fontWeight: '500',

    },
    rememberMeView: {
        width: width * 0.84,
        justifyContent: 'center',
        alignItems: 'center',
        marginTop:height * 0.05,
        marginBottom: height * 0.25,
        flexDirection:'row'
    },
})