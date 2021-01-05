import React, {useEffect, useState} from 'react';
import {
  View,
  Text,
  TouchableOpacity,
  TouchableWithoutFeedback,
  StyleSheet,
  Dimensions,
  Image,
  TextInput,
  Keyboard,
} from 'react-native';
import constants from '../../utils/constants';
import styles from './styles';
import SubmitButton from '../../components/submitButton';
import APICaller from '../../utils/APICaller';
import AsyncStorage from '@react-native-async-storage/async-storage';
import Events from '../../utils/events';
import {connect} from 'react-redux';
import {saveUser} from '../../redux/actions/user';
import AwesomeAlert from 'react-native-awesome-alerts';
import {validateEmail} from '../../utils/validateStrings';

const {height, width} = Dimensions.get('window');

const Login = (props) => {
  const [password, setPassword] = useState('');
  const [email, setEmail] = useState('');
  const [emailError, setEmailError] = useState('');
  const [showAlert, setShowAlert] = useState(false);
  const [alertMessage, setAlert] = useState('Please Fill email and password.');
  const [passwordTick, setPasswordTick] = useState(false);
  const [emailTick, setEmailTick] = useState(false);
  const [token, setToken] = useState(null);

  const {navigation, dispatch} = props;

  useEffect(() => {
    AsyncStorage.getItem('fcmToken').then((token) => {
      setToken(token);
    });
  }, []);

  const loginHandler = () => {
    if (email && !emailError && password) {
      Events.trigger('showModalLoader');
      const loginObj = {
        email,
        password,
        device_type: '1',
        fcm_token: token,
      };
      APICaller('login', 'POST', loginObj)
        .then((response) => {
          console.log('fcm_token => ', token);

          console.log('response logging in => ', response['data']);
          const {data, message, status, statusCode} = response['data'];
          if (message == 'User loggedin successfully') {
            Events.trigger('hideModalLoader');
            if (!data['is_email_verified']) {
              setAlert("You can't login without verify email.");
              setShowAlert(true);
            } else {
              AsyncStorage.setItem('userData', JSON.stringify(data));
              dispatch(saveUser(data));
              navigation.navigate('app', {screen: 'Home'});
            }
          }
        })
        .catch((error) => {
          console.log('error logging in => ', error);
          const {data, message, status, statusCode} = error['data'];
          Events.trigger('hideModalLoader');
          setAlert(message);
          setShowAlert(true);
        });
    } else if (email && emailError && password) {
      setAlert('Email is not valid.');
      setShowAlert(true);
    } else if (!email || !password) {
      setAlert('Please Fill email and password.');
      setShowAlert(true);
    }
  };

  return (
    <TouchableWithoutFeedback
      style={{flex: 1}}
      onPress={() => Keyboard.dismiss()}>
      <View style={styles.container}>
        <Image
          source={constants.images.background}
          resizeMode={'stretch'}
          style={styles.containerBackground}
        />
        <Image
          source={constants.images.formsBackground}
          resizeMode={'stretch'}
          style={styles.formsBackground}
        />
        <View style={styles.backButtonView}>
          <View
            // onPress={() => navigation.goBack()}
            style={{justifyContent: 'center', alignItems: 'center'}}>
            {/* <Image source={constants.images.backIcon} style={{ height: 18, width: 10, margin: 10 }} /> */}
          </View>
        </View>
        <View style={styles.logoView}>
          <Image source={constants.images.logo} style={{}} />
        </View>
        <View style={styles.formView}>
          <View style={styles.formWrap}>
            <View style={styles.formField}>
              <Text style={styles.fieldName}>EMAIL</Text>
              <View style={styles.fieldInputWrap}>
                <TextInput
                  style={styles.fieldInput}
                  placeholder={'john@gmail.com'}
                  returnKeyType={'done'}
                  placeholderTextColor={constants.colors.placeholder}
                  onChangeText={(text) => setEmail(text)}
                  value={email}
                  autoCapitalize={'none'}
                  onBlur={() => {
                    if (email.length) {
                      setEmailError(validateEmail(email));
                      setEmailTick(true);
                    } else {
                      setEmailTick(false);
                    }
                  }}
                />
                <View style={styles.ticWrap}>
                  {emailTick ? (
                    <Image
                      source={constants.images.inputCheck}
                      style={styles.tic}
                    />
                  ) : (
                    <View />
                  )}
                </View>
              </View>
            </View>

            <View style={styles.formField}>
              <Text style={styles.fieldName}>PASSWORD</Text>
              <View style={styles.fieldInputWrap}>
                <TextInput
                  style={styles.fieldInput}
                  placeholder={'password'}
                  returnKeyType={'done'}
                  placeholderTextColor={constants.colors.placeholder}
                  secureTextEntry={true}
                  onChangeText={(text) => setPassword(text)}
                  value={password}
                  onBlur={() => {
                    if (password.length) setPasswordTick(true);
                    else setPasswordTick(false);
                  }}
                />
                <View style={styles.ticWrap}>
                  {passwordTick ? (
                    <Image
                      source={constants.images.inputCheck}
                      style={styles.tic}
                    />
                  ) : (
                    <View />
                  )}
                </View>
              </View>
            </View>

            {/* <View style={[styles.formField, { flexDirection: 'row', justifyContent: 'flex-start', height: Dimensions.get('window').height * 0.03 }]} >
            <Image source={constants.images.radioButton} style={{ height: 20, width: 20, marginRight: 10 }} />
            <Text style={{ fontSize: 13, fontWeight: '500' }} >Remember Me</Text>
          </View> */}
            <View style={{height: 10}} />

            <View style={styles.rememberMeView}>
              <SubmitButton
                title={'LOGIN'}
                colors={['rgb(62, 218, 243)', 'rgb(191, 53, 160)']}
                submitFunction={() => loginHandler()}
              />
            </View>
            <TouchableOpacity
              onPress={() => navigation.navigate('ForgotPassword')}
              style={styles.forgotPasswordView}>
              <Text style={styles.forgotPasswordText}>Forgot Password?</Text>
            </TouchableOpacity>
          </View>
        </View>
        <View style={styles.footerView}>
          <Text style={styles.footerText}>
            Don't have an account yet?{' '}
            <Text
              onPress={() => navigation.navigate('SignUp')}
              style={styles.footerlinkText}>
              Sign Up
            </Text>
          </Text>
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
    </TouchableWithoutFeedback>
  );
};

export default connect()(Login);
