import React, { useState, } from 'react';
import {
  View,
  Text,
  TouchableOpacity,
  StyleSheet,
  Dimensions,
  Image,
  TextInput
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
import CodeInput from 'react-native-confirmation-code-input';

const { height, width } = Dimensions.get('window');

const ChangePassword = (props) => {
  const [password, setNew] = useState('');
  const [confirm, setConfirm] = useState('');
  const [showAlert, setShowAlert] = useState(false);
  const [message, setMessage] = useState('');
  const [success, setSuccess] = useState(false);
  const [otp, setOTP] = useState('');

  const { navigation, route, dispatch, userToken } = props;
  const {params} = route;

  console.log("props in reset => ",props)

  const changePasswordHandler = () => {
    if (!otp) {
      setMessage('Please Enter your 6 digits OTP.');
      setShowAlert(true);
    } else if (otp.length != 6) {
      setMessage('OTP should be 6 digits.');
      setShowAlert(true);
    } else if (!password || !confirm) {
      setMessage('Please Enter New Password and Confirm.');
      setShowAlert(true);
    } else if (password !== confirm) {
      setMessage('Please Enter The Same Password.');
      setShowAlert(true);
    } else {
      console.log("ready to go")
      const body = {
        user_id: params['user_id'],
        reset_password_otp: otp,
        password,
        confirm_password: confirm
      };
      const endpoint = 'resetPassword';
      const method = 'POST';
      const headers = {
        "Content-Type": "application/json",
        // "Authorization": `Bearer ${userToken}`,
        "Accept": "application/json"
      };
      APICaller(endpoint, method, body, headers)
        .then(response => {
          console.log('response resetting password => ', response['data']);
          const { status, statusCode, message, data } = response['data'];
          if (status === 'success') {
            // dispatch(saveUserProfile(data));
            setMessage('Your Password has reset Successfully.');
            setShowAlert(true);
            setSuccess(true);
          }
        })
        .catch(error => {
          console.log("error changing password => ", error['data']);
          const { status, statusCode, message, data } = error['data'];
          setMessage(message);
          setShowAlert(true);
        })
    }
  }

  return (
    <View style={styles.container} >
      <Image source={constants.images.background} resizeMode={'stretch'} style={styles.containerBackground} />
      <Image source={constants.images.formsBackground} resizeMode={'stretch'} style={styles.formsBackground} />
      <View style={styles.backButtonView} >
        <TouchableOpacity
          onPress={() => navigation.goBack()}
          style={{ justifyContent: 'center', alignItems: 'center', flex: 1 }}
        >
          <Image source={constants.images.backIcon} style={{ height: 18, width: 10, margin: 10 }} />
        </TouchableOpacity>
        <View style={{ flex: 8, justifyContent: 'center', alignItems: 'center' }} >
          <Text style={styles.headingText} >Reset Password</Text>
        </View>
        <View style={{ flex: 1 }} />
      </View>
      <View style={styles.formView} >

        <View style={styles.formWrap} >
          <View style={styles.formField} >
            <Text style={styles.fieldName} >OTP</Text>
            <CodeInput
              keyboardType="numeric"
              codeLength={6}
              className='border-circle'
              compareWithCode={null}
              autoFocus={true}
              onFulfill={(code) => setOTP(code)}
              codeInputStyle={styles.codeInput}
              containerStyle={styles.codeInputContainer}
            />
          </View>

          <View style={styles.formField} >
            <Text style={styles.fieldName} >NEW PASSWORD</Text>
            <View style={styles.fieldInputWrap} >
              <TextInput
                style={styles.fieldInput}
                onChangeText={text => setNew(text)}
                value={password}
                autoCapitalize={'none'}
                secureTextEntry={true}
              />
            </View>
          </View>

          <View style={styles.formField} >
            <Text style={styles.fieldName} >CONFIRM NEW PASSWORD</Text>
            <View style={styles.fieldInputWrap} >
              <TextInput
                style={styles.fieldInput}
                onChangeText={text => setConfirm(text)}
                value={confirm}
                autoCapitalize={'none'}
                secureTextEntry={true}
              />
            </View>
          </View>

          <SubmitButton
            title={'SUBMIT'}
            submitFunction={() => changePasswordHandler()}
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
          if (success)
            navigation.navigate('Login');
        }}
        onConfirmPressed={() => {
          setShowAlert(false);
          if (success)
          navigation.navigate('Login');
        }}
        onDismiss={() => {
          setShowAlert(false);
          if (success)
          navigation.navigate('Login');
        }}
      />
    </View>
  )
};

const mapStateToProps = (state) => ({
  userToken: state.user.userToken
});


export default connect(mapStateToProps)(ChangePassword);