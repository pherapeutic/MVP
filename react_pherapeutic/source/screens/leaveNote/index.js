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

const LeaveNote = (props) => {
    const [noteText, setnoteText] = useState('');
    const { userToken ,navigation} = props;
    const {caller_id} = props.route.params;
 
    const reviewHandler = () => {
        //navigation.navigate('Home');
        let finalObj = {feedback_note:noteText,call_logs_id:caller_id}
        console.log("finalobj", finalObj);
    
        const headers = {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${userToken}`,
            'Accept': 'application/json',
          };
        APICaller('therapistPostFeedback', 'POST',finalObj,headers)
            .then(response => {
                console.log('response logging in => ', response['data']);
                const { data, message, status, statusCode } = response['data'];
                if (status === 'success') {
                 navigation.navigate('Home');
                  }
               
            })


            .catch(error => {
                console.log('error logging in => ', error);
                const { data, message, status, statusCode } = error['data'];
             
              })
    }
    return (
        <View style={styles.container}>

            <Image
                source={constants.images.background}
                resizeMode={'stretch'}
                style={styles.containerBackground}
            />
            <View style={styles.viewStyle}>
    <Text style={styles.leaveNoteTextHeader}>About your session{'\n'}with James</Text>
            
            </View>
           


            <View style={styles.leaveNoteView} >

                <View style={styles.leaveNoteWrap} >

                    <Text style={styles.textStyle} >Tell us about your session</Text>
                  

                    <View style={styles.viewNoteField}>
                        <TextInput placeholder={'My client is...'}
                            placeholderTextColor={constants.colors.placeholder}
                            onChangeText={text => setnoteText(text)}
                            style={styles.fieldInput} />
                    </View>

                </View>
            </View>

            <View style={styles.rememberMeView} >
                <SubmitButton
                    title={'SUBMIT'}
                    colors={['rgb(62, 218, 243)', 'rgb(191, 53, 160)']}
                    submitFunction={() => reviewHandler()}
                />
            </View>
        </View>

    )
};

const mapStateToProps = (state) => ({
    userToken: state.user.userToken,
  });

export default connect(mapStateToProps)(LeaveNote);