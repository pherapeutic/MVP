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
  Platform,
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
  AppleButton,
  appleAuth,
} from '@invertase/react-native-apple-authentication';
//import { SignInWithAppleButton, AppleButton } from 'react-native-apple-authentication';
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
    //  GoogleSignin.configure();
    GoogleSignin.configure({
      scopes: ['email'], // what API you want to access on behalf of the user, default is email and profile
      webClientId:
        Platform.OS == 'ios'
          ? '414091408773-d3oct56c1g8otuvo8aj1gq10hj7mu17l.apps.googleusercontent.com'
          : '313896155549-9pjng4gik1g1foigcgmlpdg58qpguvrh.apps.googleusercontent.com', // client ID of type WEB for your server (needed to verify user ID and offline access)
      offlineAccess: true, // if you want to access Google API on behalf of the user FROM YOUR SERVER
      forceCodeForRefreshToken: true, // [Android] related to `serverAuthCode`, read the docs link below *.
    });
  }, []);

  const loginWithFacebook = () => {
    LoginManager.logInWithPermissions(['public_profile', 'email']).then(
      // LoginManager.logInWithReadPermissions(['public_profile', 'email']).then(
      function (result) {
        if (result.isCancelled) {
         // console.warn('Login cancelled');
        } else {
          AccessToken.getCurrentAccessToken().then((data) => {
            const accessToken = data.accessToken.toString();
            console.log('facebook token', accessToken);
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
    console.log('GoogleSignin' + GoogleSignin);
    try {
      await GoogleSignin.hasPlayServices();
      const userInfo = await GoogleSignin.signIn();
      const {user} = userInfo;
      var username = user.name.split(' ');
      console.log('username' + username);
      var result = {
        first_name: username[0],
        last_name: username.length > 1 ? username[1] : '',
        email: user.email,
        googletoken: user.id,
        image: user.photo,
      };
      socialLogin(result, result.googletoken, 1);
    } catch (error) {
      console.log('error in google signin', JSON.stringify(error));
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
    console.log('start socialLogin == >', result);
    Events.trigger('showModalLoader');
    const registerObj = {
      social_token: token,
      login_type: login_type,
    };

    console.log('register object => ', registerObj);
    APICaller('socialLogin', 'POST', registerObj)
      .then((response) => {
        Events.trigger('hideModalLoader');
        console.log('response after register social => ', response);
       // console.warn('response after register social => ', response);
        const {data, message, status, statusCode} = response['data'];
        // const { message } = data;
        if (message == 'User loggedin successfully') {
          Events.trigger('hideModalLoader');
          AsyncStorage.setItem('userData', JSON.stringify(data));
          dispatch(saveUser(data));
          navigation.replace('app', {screen: 'Home'});
        }
      })
      .catch((error) => {
      //  console.warn('error after sociallogin => ', JSON.stringify(error));
        const {data} = error;
        Events.trigger('hideModalLoader');
        if (data.statusCode === 422) {
          if (login_type == 1) {
            navigation.navigate('SignUp', {
              first_name: result.first_name,
              last_name: result.last_name,
              email:result.email,
              googletoken: token,
              image: result.image ? result.image : '',
            });
          } else if (login_type == 2) {
            navigation.navigate('SignUp', {
              first_name: result.first_name,
              last_name: result.last_name,
              email:result.email,
              fbtoken: token,
              image: result.picture ? result.picture.data.url : '',
            });
          } else if (login_type == 3) {
            navigation.navigate('SignUp', {
              first_name: result.first_name,
              last_name: result.last_name,
              email:result.email,
              appletoken: token,
              //image: result.picture ? result.picture.data.url : '',
            });
          }
        } else {
          setAlert(data.message);
          setShowAlert(true);
        }
      });
  };
  const appleSignIn = (result) => {
    console.log('Resssult', result);
    // if(result.fullName){
    // this.apiForAppleSignin(result)
    // }
  };
  const onAppleButtonPress = async () => {
    // performs login request
    const appleAuthRequestResponse = await appleAuth.performRequest({
      requestedOperation: appleAuth.Operation.LOGIN,
      requestedScopes: [appleAuth.Scope.EMAIL, appleAuth.Scope.FULL_NAME],
    });

    const {identityToken, fullName, user, email} = appleAuthRequestResponse;
    console.log(
      'authentication apple',
      JSON.stringify(appleAuthRequestResponse),
    );

    if (identityToken) {
      Events.trigger('showModalLoader');
      const registerObj = {
        apple_token: identityToken,
      };

      console.log('register object => ', registerObj);
      APICaller('appleLogin', 'POST', registerObj)
        .then((response) => {
          Events.trigger('hideModalLoader');
          // console.log('response after register => ', response.data);

          const {data, message, status, statusCode} = response['data'];
          // const { message } = data;
          // console.log('userdata' + data.user_detail);
          if (message == 'User loggedin successfully') {
            Events.trigger('hideModalLoader');
            AsyncStorage.setItem('userData', JSON.stringify(data));
            dispatch(saveUser(data));
            navigation.replace('app', {screen: 'Home'});
          } else {
            Events.trigger('hideModalLoader');
            // navigation.navigate('SignUp', {
            //   first_name: fullName.givenName ? fullName.givenName : 'N/A',
            //   last_name: fullName.familyName ? fullName.familyName : 'N/A',
            //   email: data.user_detail.email,
            //   appletoken: identityToken,
            //   //  image: result.picture ? result.picture.data.url : '',
            // });
            let resultdata = {
              first_name: fullName.givenName ? fullName.givenName : 'N/A',
              last_name: fullName.familyName ? fullName.familyName : 'N/A',
              email: email,
              appletoken: user,
            };
            socialLogin(resultdata, resultdata.appletoken, 3);
          }
        })
        .catch((error) => {
          const {data} = error;
          Events.trigger('hideModalLoader');
          if (data.statusCode === 422) {
          } else {
            setAlert(data.message);
            setShowAlert(true);
          }
        });
    }
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
          console.log('result of facebook', result);
          if (result.isCancelled) {
            console.log('Login cancelled');
          }

          // if (result.email) {
          //   Alert.alert(
          //     'Error',
          //     'To contiune MyApp plase allow access to your email',
          //     'Ok',
          //   );
          // } else {
          socialLogin(result, result.id, 2);
          //  }
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
              submitFunction={() => {
                navigation.navigate('SignUp');
              }}
              empty={true}
            />
            <View style={{height: height * 0.014}} />
          </View>
        </View>
        {/* {Platform.OS == 'android' ? ( */}
        <View style={styles.continueTextWrap}>
          <Text style={styles.continueText}>Or continue with</Text>
        </View> 
        {/* ) : null} */}
        {/* {Platform.OS == 'android' ? ( */}
         <View
          style={{
            flexDirection: 'column',
            justifyContent: 'space-between',
            alignItems: 'center',
          }}>
          <TouchableOpacity
            onPress={() => loginWithFacebook()}
            style={{
              justifyContent: 'center',
              alignItems: 'center',
              height: Dimensions.get('window').height * 0.052,
              width: Dimensions.get('window').width * 0.6,
              borderRadius: 4,
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
                  fontSize: 12,
                  textAlign: 'center',
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
              borderRadius: 4,
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
                  fontSize: 12,
                  textAlign: 'center',
                }}>
                Google
              </Text>
            </View>
          </TouchableOpacity>
          {Platform.OS == 'ios' ? (
            <View
              style={{
                justifyContent: 'center',
                alignItems: 'center',
                height: Dimensions.get('window').height * 0.052,
                width: Dimensions.get('window').width * 0.6,
                borderRadius: 4,
                backgroundColor: constants.colors.white,
                marginVertical: height * 0.01,
              }}>
              <AppleButton
                buttonStyle={AppleButton.Style.WHITE}
                buttonType={AppleButton.Type.SIGN_IN}
                style={{
                  // width: 160, // You must specify a width
                  //height: 45, // You must specify a height
                  height: Dimensions.get('window').height * 0.052,
                  width: Dimensions.get('window').width * 0.6,
                }}
                onPress={() => onAppleButtonPress()}
              />
            </View>
          ) : null}
        </View> 
          {/* ) : null} */}
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
