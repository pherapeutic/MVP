import React, {useEffect, useState} from 'react';
import {
  View,
  Text,
  TouchableOpacity,
  StyleSheet,
  Dimensions,
  Image,
  TextInput,
  Alert,
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
import {GoogleSignin, statusCodes} from '@react-native-community/google-signin';

import {
  LoginManager,
  AccessToken,
  GraphRequest,
  GraphRequestManager,
} from 'react-native-fbsdk';

const {height, width} = Dimensions.get('window');

const Login = (props) => {
  const [password, setPassword] = useState('');

  const {navigation, dispatch} = props;
  const [alertMessage, setAlert] = useState('');
  const [showAlert, setShowAlert] = useState(false);

  useEffect(() => {
    GoogleSignin.configure();
  }, []);

  const loginWithFacebook = () => {
    LoginManager.logInWithPermissions(['public_profile', 'email']).then(
      function (result) {
        if (result.isCancelled) {
          console.log('Login cancelled');
        } else {
          AccessToken.getCurrentAccessToken().then((data) => {
            const accessToken = data.accessToken.toString();
            getInfoFromToken(accessToken);
          });
        }
      },
      function (error) {
        console.log('Login fail with error: ' + error);
      },
    );
  };

  const signInWithGoogle = async () => {
    try {
      await GoogleSignin.hasPlayServices();
      const userInfo = await GoogleSignin.signIn();
      const {user} = userInfo;
      var username = user.name.split(' ');
      var result = {
        first_name: username[0],
        last_name: username.length > 1 ? username[1] : '',
        email: user.email,
        googletoken: user.id,
        image: user.photo,
      };
      socialLogin(result, result.googletoken, 1);
    } catch (error) {
     // alert(JSON.stringify(error));
      if (error.code === statusCodes.SIGN_IN_CANCELLED) {
        // user cancelled the login flow
      } else if (error.code === statusCodes.IN_PROGRESS) {
        // operation (e.g. sign in) is in progress already
      } else if (error.code === statusCodes.PLAY_SERVICES_NOT_AVAILABLE) {
        // play services not available or outdated
      } else {
        // some other error happened
      }
    }
  };

  const socialLogin = async (result, token, login_type) => {
    Events.trigger('showModalLoader');
    const registerObj = {
      social_token: token,
      login_type: login_type,
    };

    console.log('register object => ', registerObj);
    APICaller('socialLogin', 'POST', registerObj)
      .then((response) => {
        Events.trigger('hideModalLoader');
        console.warn('response after register => ', response);
        const {data, message, status, statusCode} = response['data'];
        // const { message } = data;
        if (message == 'User loggedin successfully') {
          Events.trigger('hideModalLoader');
          AsyncStorage.setItem('userData', JSON.stringify(data));
          dispatch(saveUser(data));
          navigation.navigate('app', {screen: 'Home'});
        }
      })
      .catch((error) => {
         console.warn('error after sociallogin => ', error);
        const {data} = error;
        Events.trigger('hideModalLoader');
        if (data.statusCode === 422) {
          if (login_type == 1) {
            navigation.navigate('SignUp', {
              first_name: result.first_name,
              last_name: result.last_name,
              email: result.email,
              googletoken: token,
              image: result.image ? result.image : '',
            });
          } else {
            navigation.navigate('SignUp', {
              first_name: result.first_name,
              last_name: result.last_name,
              email: result.email,
              fbtoken: token,
              image: result.picture ? result.picture.data.url : '',
            });
          }
        } else {
          setAlert(data.message);
          setShowAlert(true);
        }
      });
  };

  function getInfoFromToken(token) {
    const PROFILE_REQUEST_PARAMS = {
      fields: {
        string: 'id, name, first_name, last_name, email, picture.type(large)',
      },
    };
    const profileRequest = new GraphRequest(
      '/me',
      {token, parameters: PROFILE_REQUEST_PARAMS},
      (error, result) => {
        if (error) {
          console.log('Login Info has an error:', error);
        } else {
          if (result.isCancelled) {
            console.log('Login cancelled');
          }
          if (result.email === undefined) {
            Alert.alert(
              'Error',
              'To contiune MyApp plase allow access to your email',
              'Ok',
            );
          } else {
            socialLogin(result, result.id, 2);
          }
        }
      },
    );
    new GraphRequestManager().addRequest(profileRequest).start();
  }
  return (
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
        <View style={{justifyContent: 'center', alignItems: 'center'}}>
          <Image
            source={constants.images.backIconn}
            style={{height: 18, width: 10, margin: 10}}
          />
        </View>
      </View>
      <View style={styles.logoView}>
        <Image source={constants.images.logo} style={{}} />
      </View>
      <View style={styles.formView}>
        <View style={styles.formWrap}>
          <View style={styles.buttonsWrap}>
            <View style={{height: height * 0.014}} />
            <SubmitButton
              title={'Login'}
              submitFunction={() => navigation.navigate('Login')}
            />
          </View>

          <View style={styles.buttonsWrap}>
            <SubmitButton
              title={'Signup'}
              submitFunction={() => navigation.navigate('SignUp')}
              empty={true}
            />
            <View style={{height: height * 0.014}} />
          </View>
        </View>
        <View style={styles.continueTextWrap}>
          <Text style={styles.continueText}>Or continue with</Text>
        </View>

        <TouchableOpacity
          onPress={() => loginWithFacebook()}
          style={{
            justifyContent: 'center',
            alignItems: 'center',
            height: Dimensions.get('window').height * 0.052,
            width: Dimensions.get('window').width * 0.6,
            borderRadius: 3,
            backgroundColor: constants.colors.white,
            marginVertical: height * 0.01,
          }}>
          <View
            style={{
              justifyContent: 'center',
              alignItems: 'center',
              flexDirection: 'row',
            }}>
            <Image
              source={constants.images.ic_fb}
              style={{height: 13, width: 13, margin: 8}}
            />
            <Text
              style={{
                color: constants.colors.black,
                fontWeight: '500',
                fontSize: 16,
              }}>
              Facebook
            </Text>
          </View>
        </TouchableOpacity>

        <TouchableOpacity
          onPress={() => signInWithGoogle()}
          style={{
            justifyContent: 'center',
            alignItems: 'center',
            height: Dimensions.get('window').height * 0.052,
            width: Dimensions.get('window').width * 0.6,
            borderRadius: 3,
            backgroundColor: constants.colors.white,
            marginVertical: height * 0.01,
          }}>
          <View
            style={{
              justifyContent: 'center',
              alignItems: 'center',
              flexDirection: 'row',
            }}>
            <Image
              source={constants.images.ic_google}
              style={{height: 13, width: 13, margin: 8}}
            />
            <Text
              style={{
                color: constants.colors.black,
                fontWeight: '500',
                fontSize: 16,
              }}>
              Google
            </Text>
          </View>
        </TouchableOpacity>
      </View>
      <View style={styles.footerView}>
        <Text style={styles.footerText}></Text>
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
  );
};
export default connect()(Login);
