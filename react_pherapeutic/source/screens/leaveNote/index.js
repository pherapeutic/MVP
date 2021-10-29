import React, { useEffect, useState } from 'react';
import {
    View,
    Text,
    Image,
    TextInput,
} from 'react-native';
import constants from '../../utils/constants';
import SubmitButton from '../../components/submitButton';
import styles from './styles';
import { connect } from 'react-redux';
import AsyncStorage from '@react-native-async-storage/async-storage';
import APICaller from '../../utils/APICaller';
import AwesomeAlert from 'react-native-awesome-alerts';
const LeaveNote = (props) => {
    const [noteText, setnoteText] = useState('');
    const [showAlert, setShowAlert] = useState(false);
    const [alertMessage, setAlert] = useState('Please fill comment');
    const { userToken, navigation } = props;
    const { caller_id, CallReciverName } = props.route.params;

    const skip = () => {
        navigation.navigate('Home');
    }
    const reviewHandler = () => {
        //navigation.navigate('Home');
        let finalObj = { feedback_note: noteText, call_logs_id: caller_id }
        console.log("finalobj", finalObj);
        if (noteText != '') {
            const headers = {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${userToken}`,
                'Accept': 'application/json',
            };
            APICaller('therapistPostFeedback', 'POST', finalObj, headers)
                .then(response => {
                    console.log('response logging in => ', response['data']);
                    const { data, message, status, statusCode } = response['data'];
                    if (status === 'success') {
                        navigation.navigate('Home');
                    }


                })


                .catch(error => {
                    console.log('error logging in => ', error);
                    navigation.navigate('Home');
                    const { data, message, status, statusCode } = error['data'];


                })
        }
        else {
            setShowAlert(true)
        }
    }
    return (
        <View style={styles.container}>

            <Image
                source={constants.images.background}
                resizeMode={'stretch'}
                style={styles.containerBackground}
            />
            <View style={styles.viewStyle}>
                <Text style={styles.leaveNoteTextHeader}>About your session{'\n'}with {CallReciverName}</Text>

            </View>



            <View style={styles.leaveNoteView} >

                <View style={styles.leaveNoteWrap} >

                    <Text style={styles.textStyle} >Tell us about your session</Text>


                    <View style={styles.viewNoteField}>
                        {/* <TextInput placeholder={'My client is...'}
                            placeholderTextColor={constants.colors.placeholder}
                            onChangeText={text => setnoteText(text)}
                            style={styles.fieldInput}
                             /> */}
                        <TextInput
                            placeholder={'My client is...'}
                            placeholderTextColor={constants.colors.black}
                            style={styles.fieldInput}
                            onChangeText={text => setnoteText(text)}
                            value={noteText}
                            autoCapitalize={'none'}
                            multiline={true}
                            numberOfLines={10}
                        />

                    </View>

                </View>
            </View>

            <View style={styles.rememberMeView} >
                <View style={{ margin: 5 }}>
                    <SubmitButton
                        title={'SKIP'}
                        size={'Medium'}
                        colors={['rgb(62, 218, 243)', 'rgb(191, 53, 160)']}
                        submitFunction={() => skip()}
                    />

                </View>
                <View style={{ margin: 5 }}>
                    <SubmitButton
                        size={'Medium'}
                        title={'SUBMIT'}
                        colors={['rgb(62, 218, 243)', 'rgb(191, 53, 160)']}
                        submitFunction={() => reviewHandler()}
                    />
                </View>
            </View>
            <AwesomeAlert
                show={showAlert}
                showProgress={false}
                message={alertMessage}
                closeOnTouchOutside={true}
                showConfirmButton={true}
                confirmText="Confirm"
                confirmButtonColor={constants.colors.lightGreen}
                onCancelPressed={() => {
                    setShowAlert(false);
                }}
                onConfirmPressed={() => {
                    setShowAlert(false);
                }}
                onDismiss={() => {
                    setShowAlert(false);
                }}
            />
        </View>

    )
};

const mapStateToProps = (state) => ({
    userToken: state.user.userToken,
});

export default connect(mapStateToProps)(LeaveNote);