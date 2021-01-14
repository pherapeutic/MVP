import React, { useState } from 'react';
import {
  View,
  Text,
  TouchableOpacity,
  StyleSheet,
  Dimensions,
  Image,
  TextInput,
} from 'react-native';
import constants from '../../utils/constants';
import styles from './styles';
import SubmitButton from '../../components/submitButton';
import APICaller from '../../utils/APICaller';
import AsyncStorage from '@react-native-async-storage/async-storage';
import Events from '../../utils/events';
import { connect } from 'react-redux';
import { saveUser } from '../../redux/actions/user';
import AwesomeAlert from 'react-native-awesome-alerts';
import { validateEmail } from '../../utils/validateStrings';
import Header from '../../components/Header';

const { height, width } = Dimensions.get('window');

const ContactUs = (props) => {
  const [Name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [subject, setSubject] = useState('');
  const [textMessage, setTextMessage] = useState('');
  const [showAlert, setShowAlert] = useState(false);
  const [message, setMessage] = useState('');
  const [success, setSuccess] = useState(false);

  const { navigation, dispatch, userToken } = props;

  const onChangeContactUsPressed = () => {
    if (!Name) {
      setMessage('Please Enter Name.');
      setShowAlert(true);
    } else if (!email) {
      setMessage('Please Enter Email.');
      setShowAlert(true);
    } else if (!subject) {
      setMessage('Please Enter subject.');
      setShowAlert(true);
    } else if (!textMessage) {
      setMessage('Please Enter message.');
      setShowAlert(true);
    } else {
      Events.trigger('showModalLoader');
      const body = {
        name: Name,
        email: email,
        subject: subject,
        message: textMessage,
      };
      const endpoint = 'contact-us';
      const method = 'POST';
      const headers = {
        'Content-Type': 'application/json',
        Authorization: `Bearer ${userToken}`,
        Accept: 'application/json',
      };
      APICaller(endpoint, method, body, headers)
        .then((response) => {

          console.log('response logging in => ', response['data']);
          const { data, message, status, statusCode } = response['data'];
          setMessage(message);
          setShowAlert(true);
          setTextMessage('');
          setEmail('');
          setName('');
          setSubject('');
          Events.trigger('hideModalLoader');
        })
        .catch((error) => {
          console.log('error logging in => ', error);
          Events.trigger('hideModalLoader');

        });
    }

  };

  return (
    <View style={styles.container}>
      <Image
        source={constants.images.background}
        resizeMode={'stretch'}
        style={styles.containerBackground}
      />
      <Header title="Contact Us" navigation={navigation} />
      <View style={styles.formView}>
        <View style={styles.formWrap}>
          <View style={styles.formField}>
            <Text style={styles.fieldName}>NAME</Text>
            <View style={styles.fieldInputWrap}>
              <TextInput
                style={styles.fieldInput}
                autoFocus={true}
                onChangeText={(text) => setName(text)}
                value={Name}
                autoCapitalize={'none'}
              />
            </View>
          </View>
          <View style={styles.formField}>
            <Text style={styles.fieldName}>EMAIL</Text>
            <View style={styles.fieldInputWrap}>
              <TextInput
                style={styles.fieldInput}
                onChangeText={(text) => setEmail(text)}
                value={email}
                autoCapitalize={'none'}
              />
            </View>
          </View>
          <View style={styles.formField}>
            <Text style={styles.fieldName}>SUBJECT</Text>
            <View style={styles.fieldInputWrap}>
              <TextInput
                style={styles.fieldInput}
                onChangeText={(text) => setSubject(text)}
                value={subject}
                autoCapitalize={'none'}
              />
            </View>
          </View>
          <View style={styles.formField}>
            <Text style={styles.fieldName}>MESSAGE</Text>
            <View style={[styles.fieldInputWrap]}>
              <TextInput
                style={[styles.fieldInput, { height: 100 }]}
                onChangeText={(text) => setTextMessage(text)}
                value={textMessage}
                autoCapitalize={'none'}
                multiline={true}
                numberOfLines={10}
              />
            </View>
          </View>

          <SubmitButton
            title={'SUBMIT'}
            submitFunction={() => onChangeContactUsPressed()}
          />
        </View>
      </View>
      <AwesomeAlert
        show={showAlert}
        showProgress={false}
        message={message}
        closeOnTouchOutside={true}
        showConfirmButton={true}
        confirmText="Confirm"
        confirmButtonColor={constants.colors.lightGreen}
        onCancelPressed={() => {
          setShowAlert(false);
          if (success) navigation.goBack();
        }}
        onConfirmPressed={() => {
          setShowAlert(false);
          if (success) navigation.goBack();
        }}
        onDismiss={() => {
          setShowAlert(false);
          if (success) navigation.goBack();
        }}
      />
    </View>
  );
};

const mapStateToProps = (state) => ({
  userToken: state.user.userToken,
});

export default connect(mapStateToProps)(ContactUs);
