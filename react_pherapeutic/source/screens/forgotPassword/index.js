import React, { useState, } from 'react';
import {
  View,
  Text,
  TouchableOpacity,
  TouchableWithoutFeedback,
  StyleSheet,
  Dimensions,
  Image,
  TextInput, Keyboard
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

const { height, width } = Dimensions.get('window');

const ForgotPassword = (props) => {
  const [password, setPassword] = useState('');
  const [email, setEmail] = useState('');
  const [emailError, setEmailError] = useState('');
  const [showAlert, setShowAlert] = useState(false);
  const [alertMessage, setAlert] = useState('Please Enter your email.');
  const [passwordTick, setPasswordTick] = useState(false);
  const [emailTick, setEmailTick] = useState(false);
  const [userId, setUserId] = useState(null);

  const { navigation, dispatch } = props;

  const sendOtp = () => {
    if (email && !emailError) {
      Events.trigger('showModalLoader');
      const endpoint = 'forgotPassword';
      const method = 'POST';
      const body = { email };

      APICaller(endpoint, method, body)
        .then(response => {
          console.log('response sending OTP => ', response['data']);
          const { data, message, status, statusCode } = response['data'];
          if (status == 'success') {
            Events.trigger('hideModalLoader');
            setAlert("Please Verify OTP");
            setShowAlert(true)
            setUserId(data['user_id'])
          }
        })
        .catch(error => {
          console.log('error sending OTP => ', error);
          const { data, message, status, statusCode } = error['data'];
          Events.trigger("hideModalLoader")
          setAlert(message);
          setShowAlert(true)
        })
    } else if (email && emailError) {
      setAlert('Email is not valid.')
      setShowAlert(true);
    } else if (!email) {
      setAlert('Please enter your Email.')
      setShowAlert(true);
    }
  }

  return (
    <TouchableWithoutFeedback
      style={{ flex: 1 }}
      onPress={() => Keyboard.dismiss()}
    >
      <View style={styles.container} >
        <Image source={constants.images.background} resizeMode={'stretch'} style={styles.containerBackground} />
        <Image source={constants.images.formsBackground} resizeMode={'stretch'} style={styles.formsBackground} />
        <View style={styles.backButtonView} >
          <View
            style={{ justifyContent: 'center', alignItems: 'center' }}
          >
          </View>
        </View>
        <View style={styles.logoView} >
          <Image source={constants.images.logo} style={{}} />
        </View>
        <View style={styles.formView} >

          <View style={styles.formWrap} >
            <View style={styles.formField} >
              <Text style={styles.fieldName} >EMAIL</Text>
              <View style={styles.fieldInputWrap} >
                <TextInput
                  style={styles.fieldInput}
                  placeholder={'john@gmail.com'}
                  returnKeyType={'done'}
                  placeholderTextColor={constants.colors.placeholder}
                  onChangeText={text => setEmail(text)}
                  value={email}
                  autoCapitalize={'none'}
                  onBlur={() => {
                    if (email.length) {
                      setEmailError(validateEmail(email))
                      setEmailTick(true)
                    } else {
                      setEmailTick(false)
                    }
                  }}
                />
                <View style={styles.ticWrap} >
                  {
                    emailTick
                      ?
                      <Image source={constants.images.inputCheck} style={styles.tic} />
                      :
                      <View />
                  }
                </View>
              </View>
            </View>
            <View style={{ height: 10 }} />

            <View style={styles.rememberMeView} >
              <SubmitButton
                title={'SUBMIT'}
                colors={['rgb(62, 218, 243)', 'rgb(191, 53, 160)']}
                submitFunction={() => sendOtp()}
              />
            </View>
            <View style={styles.forgotPasswordView} >
              <Text style={styles.forgotPasswordText} ></Text>
            </View>
          </View>
        </View>
        <View style={styles.footerView} >
          <Text style={styles.footerText} >Don't have an account yet? <Text
            onPress={() => navigation.navigate('SignUp')}
            style={styles.footerlinkText}
          >Sign Up</Text></Text>
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
            if (userId) {
              navigation.navigate('ResetPassword', { user_id: userId })
            }
          }}
          onConfirmPressed={() => {
            setShowAlert(false);
            if (userId) {
              navigation.navigate('ResetPassword', { user_id: userId })
            }
          }}
          onDismiss={() => {
            setShowAlert(false);
            if (userId) {
              navigation.navigate('ResetPassword', { user_id: userId })
            }
          }}
        />
      </View>
    </TouchableWithoutFeedback>
  )
};
export default connect()(ForgotPassword);