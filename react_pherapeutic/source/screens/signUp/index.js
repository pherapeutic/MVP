import React, { useEffect, useState } from 'react';
import {
  View,
  Text,
  TouchableOpacity,
  StyleSheet,
  Dimensions,
  Image,
  TextInput,
  ScrollView,
  KeyboardAvoidingView,
} from 'react-native';
import styles from './styles';
import { KeyboardAwareScrollView } from 'react-native-keyboard-aware-scroll-view';
import { validateEmail } from '../../utils/validateStrings';
import AwesomeAlert from 'react-native-awesome-alerts';
import Geolocation from '@react-native-community/geolocation';
import constants from '../../utils/constants';
import Events from '../../utils/events';
import APICaller from '../../utils/APICaller';
import SubmitButton from '../../components/submitButton';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { saveUser } from '../../redux/actions/user';
import { connect } from 'react-redux';
import CheckBox from '@react-native-community/checkbox';

const { height, width } = Dimensions.get('window');

const SignUp = (props) => {
  const [first_name, setFirstName] = useState('');
  const [last_name, setLastName] = useState('');
  const [email, setEmail] = useState('');
  const [emailError, setEmailError] = useState('');
  const [password, setPassword] = useState('');
  const [confirm_password, setConfirmPassword] = useState('');
  const [role, setRole] = useState('');
  const [language, setLanguage] = useState('');
  const [language_id, setLanguage_id] = useState('');
  const [showRoles, setShowRoles] = useState(false);
  const [showLanguages, setShowLanguages] = useState(false);
  const [showSpecialism, setShowSpecialism] = useState(false);
  const [selectedSpeciality, setSpeciality] = useState('');
  const [speciality_id, setSpeciality_id] = useState('');
  const [years, setYears] = useState('');
  const [languages, storeLanguages] = useState([]);
  const [location, storeLocation] = useState([]);
  const [specialities, storeSpecialities] = useState([]);
  const [showAlert, setShowAlert] = useState(false);
  const [alertMessage, setAlert] = useState('Please Fill All Fields');
  const [readyToSubmit, setReadyToSibmit] = useState(true);
  const [token, setToken] = useState(null);
  const [toggleCheckBox, setToggleCheckBox] = useState(false)

  const { navigation, route, dispatch } = props;
  const { params } = route;

  // const {params} = props.navigation.state;

  useEffect(() => {
    getLanguages();
    getSpecialities();

    if (params && (params.fbtoken || params.googletoken)) {
      setFirstName(params.first_name);
      setLastName(params.last_name);
      setEmail(params.email);
    }

    AsyncStorage.getItem('fcmToken').then((token) => {
      setToken(token);
    });
  }, []);

  const getLanguages = () => {
    APICaller('getLanguages', 'GET')
      .then((response) => {
        console.log('response getting languages => ', response);
        const { data, message, status, statusCode } = response['data'];
        if (status === 'success') {
          storeLanguages([...data]);
        }
      })
      .catch((error) => {
        console.log('error getting languages => ', error);
      });
  };

  const getCurrentCoordinates = async () => {
    await Geolocation.getCurrentPosition((info) => storeLocation(info));
    console.log('location is', location);
  };

  const getSpecialities = () => {
    APICaller('getTherapistTypes', 'GET')
      .then((response) => {
        console.log('response getting specialiites => ', response);
        const { data, message, status, statusCode } = response['data'];
        if (status === 'success') {
          storeSpecialities([...data]);
        }
      })
      .catch((error) => {
        console.log('error getting specialiites => ', error);
      });
  };

  const registerUser = async () => {
    Events.trigger('showModalLoader');

    // var latitude = '31.104605';
    // var longitude = '77.173424';
    var latitude = '';
    var longitude = '';

    if (location && location.coords) {
      latitude = location.coords.latitude;
      longitude = location.coords.longitude;
    }

    var registerObj = {
      first_name,
      last_name,
      email,
      password,
      confirm_password,
      role: role == 'Client' ? '0' : '1',
      languages: [language_id],

      experience: years,
      specialism: [speciality_id],

      device_type: '1',
      fcm_token: token,
      qualification: '5',
      address: 'ABC place',
      latitude: latitude,
      longitude: longitude,
    };
    if (params && (params.fbtoken || params.googletoken)) {
      registerObj.image = params.image;
      if (params.fbtoken) {
        registerObj.social_token = params.fbtoken;
        registerObj.login_type = 2;
      } else if (params.googletoken) {
        registerObj.social_token = params.googletoken;
        registerObj.login_type = 1;
      }

      // only for testing purpose
      // registerObj.social_token = '867672634855435435633847';
      // registerObj.email = 'abcd454837554544@gmail.com';
      // registerObj.login_type = 2;
    }
    console.log('register object => ', registerObj);
    // alert(JSON.stringify(registerObj));
    APICaller(
      params && (params.fbtoken || params.googletoken)
        ? 'socialLogin'
        : 'register',
      'POST',
      registerObj,
    )
      .then((response) => {
        Events.trigger('hideModalLoader');
        console.warn('response after register => ', response);
        const { data, message, status, statusCode } = response['data'];
        // const { message } = data;

        if (params && (params.fbtoken || params.googletoken)) {
          AsyncStorage.setItem('userData', JSON.stringify(data));
          dispatch(saveUser(data));
          navigation.navigate('Onboarding');
        } else if (message === 'Your account created successfully.') {
          navigation.navigate('VerifyEmail', { user_id: data['user_id'] });
        }
      })
      .catch((error) => {
        console.warn('error after register => ', error);
        const { data } = error;
        if (data) {
          Events.trigger('hideModalLoader');
          setAlert(data.message);
          setShowAlert(true);
        }
      });
  };

  const signUpHandler = () => {
    if (!(params && (params.fbtoken || params.googletoken))) {
      if (
        first_name &&
        last_name &&
        email &&
        password &&
        confirm_password &&
        role &&
        language_id &&
        password === confirm_password && confirm_password.length >= 6 && password.length >= 6
      ) {
        if (role == 'Therapist') {
          if (!years) {
            setAlert('Please Enter Total Experience.');
            setShowAlert(true);
          } else if (!selectedSpeciality) {
            setAlert('Please Select Speciality');
            setShowAlert(true);
          } else {
            // call api
            registerUser();
          }
        } else {
          // call api
          registerUser();
        }
      } else {
        if (!first_name) setAlert('Please Enter First Name.');
        else if (!last_name) setAlert('Please Enter Last Name.');
        else if (!email) setAlert('Please Enter Email Address.');
        else if (emailError) setAlert('Please Enter Valid Email Address.');
        else if (!password) setAlert('Please Enter Password.');
        else if (!confirm_password) setAlert('Please Enter Confirm Password.');
        else if (confirm_password !== password) setAlert('Please Enter Same Password.');
        else if (!role) setAlert('Please Select Your Role.');
        else if (!language) setAlert('Please Select Your Language.');
        else if (password.length < 6) setAlert('Please Fill password at least 6 digit.');
        else if (confirm_password.length < 6) setAlert('Please Fill password at least 6 digit.');
        setShowAlert(true);
      }
    }
    else {
      if (
        first_name &&
        last_name &&
        email &&
        role &&
        language_id
      ) {
        if (role == 'Therapist') {
          if (!years) {
            setAlert('Please Enter Total Experience.');
            setShowAlert(true);
          } else if (!selectedSpeciality) {
            setAlert('Please Select Speciality');
            setShowAlert(true);
          } else {
            // call api
            registerUser();
          }
        } else {
          // call api
          registerUser();
        }
      } else {
        if (!first_name) setAlert('Please Enter First Name.');
        else if (!last_name) setAlert('Please Enter Last Name.');
        else if (!email) setAlert('Please Enter Email Address.');
        else if (emailError) setAlert('Please Enter Valid Email Address.');

        else if (!role) setAlert('Please Select Your Role.');
        else if (!language) setAlert('Please Select Your Language.');
        setShowAlert(true);
      }

    }
  };

  const ListView = () => (
    <ScrollView >
      <View style={{ justifyContent: 'center', alignItems: 'center' }}>
        {languages.map((lang) => {
          console.log('!!!!!!!!!!');
          return (
            <TouchableOpacity
              onPress={() => {
                setLanguage_id(lang['id']);
                setLanguage(lang['title']);
                setShowLanguages(false);
              }}
              style={{
                height: Dimensions.get('window').height * 0.05,
                width: Dimensions.get('window').width * 0.6,
                paddingHorizontal: 2,
                justifyContent: 'center',
                alignItems: 'flex-start',
              }}>
              <Text>{lang['title']}</Text>
            </TouchableOpacity>
          );
        })}
      </View>
    </ScrollView>
  );

  const SpecialitiesList = () => (
    <View style={{ justifyContent: 'center', alignItems: 'center' }}>
      {specialities.map((speciality) => {
        console.log('!!!!!!!!!!');
        return (
          <TouchableOpacity
            onPress={() => {
              setSpeciality_id(speciality['id']);
              setSpeciality(speciality['title']);
              setShowSpecialism(false);
            }}
            style={{
              height: Dimensions.get('window').height * 0.05,
              width: Dimensions.get('window').width * 0.6,
              paddingHorizontal: 2,
              justifyContent: 'center',
              alignItems: 'flex-start',
            }}>
            <Text>{speciality['title']}</Text>
          </TouchableOpacity>
        );
      })}
    </View>
  );

  const Roles = () => (
    <View style={{ justifyContent: 'center', alignItems: 'center' }}>
      <TouchableOpacity
        onPress={() => {
          setRole('Client');
          setShowRoles(false);
        }}
        style={{
          height: Dimensions.get('window').height * 0.05,
          width: Dimensions.get('window').width * 0.6,
          paddingHorizontal: 2,
          justifyContent: 'center',
          alignItems: 'flex-start',
        }}>
        <Text>Client</Text>
      </TouchableOpacity>
      <TouchableOpacity
        onPress={() => {
          setRole('Therapist');
          setShowRoles(false);
        }}
        style={{
          height: Dimensions.get('window').height * 0.05,
          width: Dimensions.get('window').width * 0.6,
          paddingHorizontal: 2,
          justifyContent: 'center',
          alignItems: 'flex-start',
        }}>
        <Text>Therapist</Text>
      </TouchableOpacity>
    </View>
  );

  return (
    // <KeyboardAwareScrollView
    //   showsVerticalScrollIndicator={false}
    //   contentContainerStyle={{ width: '100%', flex: 1 }}
    //   keyboardVerticalOffset={'60'}
    //   behavior={'padding'}
    // >
    <View style={{ flex: 1 }}>
      <Image
        source={constants.images.background}
        resizeMode={'stretch'}
        style={styles.containerBackground}
      />

      <ScrollView
        showsVerticalScrollIndicator={false}
        alwaysBounceVertical={false}
        contentContainerStyle={styles.container}>
        <Image
          source={constants.images.formsBackground}
          resizeMode={'stretch'}
          style={styles.formsBackground}
        />
        <View style={[styles.backButtonView, {}]}>
          <TouchableOpacity
            onPress={() => navigation.goBack()}
            style={{ flex: 1.5, justifyContent: 'center', alignItems: 'center' }}>
            <Image
              source={constants.images.backIcon}
              style={{ height: 20, width: 10, margin: 10 }}
            />
          </TouchableOpacity>
          <View
            style={{
              flex: 7,
              justifyContent: 'center',
              alignItems: 'center',
              marginTop: 30,
            }}>
            <Image
              source={constants.images.logo}
              resizeMode={'contain'}
              style={{
                height: Dimensions.get('window').height * 0.09,
                width: Dimensions.get('window').width * 0.75,
              }}
            />
          </View>
          <View style={{ flex: 1.5 }} />
        </View>
        <View style={styles.formView}>
          <View style={styles.formWrap}>
            <View style={styles.formField}>
              <Text style={styles.fieldName}>FIRST NAME</Text>
              <View style={styles.fieldInputWrap}>
                <TextInput
                  style={styles.fieldInput}
                  onChangeText={(text) => setFirstName(text)}
                  value={first_name}
                  autoCompleteType={'off'}
                  autoCorrect={false}
                  editable={!(params && (params.fbtoken || params.googletoken))}
                  onFocus={() => getCurrentCoordinates()}
                />
              </View>
            </View>

            <View style={styles.formField}>
              <Text style={styles.fieldName}>LAST NAME</Text>
              <View style={styles.fieldInputWrap}>
                <TextInput
                  style={styles.fieldInput}
                  onChangeText={(text) => setLastName(text)}
                  value={last_name}
                  editable={!(params && (params.fbtoken || params.googletoken))}
                  autoCompleteType={'off'}
                  autoCorrect={false}
                />
              </View>
            </View>
            <View style={styles.formField}>
              <Text style={styles.fieldName}>EMAIL ADDRESS</Text>
              <View style={styles.fieldInputWrap}>
                <TextInput
                  style={styles.fieldInput}
                  onChangeText={(text) => setEmail(text)}
                  value={email}
                  autoCompleteType={'off'}
                  autoCorrect={false}
                  // editable={!(params && (params.fbtoken || params.googletoken))}
                  autoCapitalize={'none'}
                  onBlur={() => {
                    if (email.length) setEmailError(validateEmail(email));
                  }}
                />
              </View>
            </View>
            {!(params && (params.fbtoken || params.googletoken)) && (
              <View style={styles.formField}>
                <Text style={styles.fieldName}>CREATE PASSWORD</Text>
                <View style={styles.fieldInputWrap}>
                  <TextInput
                    style={styles.fieldInput}
                    secureTextEntry={true}
                    onChangeText={(text) => setPassword(text)}
                    value={password}
                    autoCompleteType={'off'}
                    autoCorrect={false}
                    maxLength={8}
                  />
                </View>
              </View>
            )}
            {!(params && (params.fbtoken || params.googletoken)) && (
              <View style={styles.formField}>
                <Text style={styles.fieldName}>CONFIRM PASSWORD</Text>
                <View style={styles.fieldInputWrap}>
                  <TextInput
                    style={styles.fieldInput}
                    secureTextEntry={true}
                    onChangeText={(text) => setConfirmPassword(text)}
                    maxLength={8}
                    value={confirm_password}></TextInput>
                </View>
              </View>
            )}



            <View style={styles.formField}>
              <Text style={styles.fieldName}>
                ARE YOU A THERAPIST OR A CLIENT?
              </Text>
              <View style={styles.fieldInputWrap}>
                <TextInput
                  style={[
                    styles.fieldInput,
                    { width: Dimensions.get('window').width * 0.7 },
                  ]}
                  onChangeText={(text) => setLanguage(text)}
                  value={role}
                  editable={false}
                  autoCompleteType={'off'}
                  autoCorrect={false}
                />
                <TouchableOpacity onPress={() => setShowRoles(true)}>
                  <Image
                    style={{ height: 25, width: 25, margin: 3 }}
                    source={constants.images.downArrow}
                  />
                </TouchableOpacity>
              </View>
            </View>

            {role == 'Therapist' ? (
              <View style={{ justifyContent: 'center', alignItems: 'center' }}>
                <View style={styles.formField}>
                  <Text style={styles.fieldName}>SPECIALISM</Text>
                  <View style={styles.fieldInputWrap}>
                    <TextInput
                      style={[
                        styles.fieldInput,
                        { width: Dimensions.get('window').width * 0.7 },
                      ]}
                      // onChangeText={text => setLanguage(text)}
                      value={selectedSpeciality}
                      editable={false}
                      autoCompleteType={'off'}
                      autoCorrect={false}
                    />
                    <TouchableOpacity onPress={() => setShowSpecialism(true)}>
                      <Image
                        style={{ height: 25, width: 25, margin: 3 }}
                        source={constants.images.downArrow}
                      />
                    </TouchableOpacity>
                  </View>
                </View>

                <View style={styles.formField}>
                  <Text style={styles.fieldName}>YEARS OF EXPERIENCE</Text>
                  <View style={styles.fieldInputWrap}>
                    <TextInput
                      style={styles.fieldInput}
                      onChangeText={(text) => setYears(text)}
                      value={years}
                      autoCompleteType={'off'}
                      autoCorrect={false}
                      keyboardType={'number-pad'}
                    />
                  </View>
                </View>
              </View>
            ) : (
                <View />
              )}

            <View style={styles.formField}>
              <Text style={styles.fieldName}>LANGUAGE YOU SPEAK</Text>
              <View style={styles.fieldInputWrap}>
                <TextInput
                  style={[
                    styles.fieldInput,
                    { width: Dimensions.get('window').width * 0.7 },
                  ]}
                  onChangeText={(text) => setLanguage(text)}
                  value={language}
                  editable={false}
                />
                <TouchableOpacity onPress={() => setShowLanguages(true)}>
                  <Image
                    style={{ height: 25, width: 25, margin: 3 }}
                    source={constants.images.downArrow}
                  />
                </TouchableOpacity>
              </View>
            </View>

            <SubmitButton
              title={'SIGN UP'}
              submitFunction={() => signUpHandler()}
            />
          </View>
        </View>
        {/* <View style={styles.footerView}>
          <Text style={[styles.footerText]}>
            Already signed up?{' '}
            <Text
              onPress={() => navigation.navigate('Login')}
              style={styles.footerlinkText}>
              Login
            </Text>


          </Text>
          <View style={{flexDirection:'row'}}>
          <CheckBox
            disabled={false}
            value={toggleCheckBox}
            onValueChange={(newValue) => setToggleCheckBox(newValue)}
            tintColors={{true: '#228994'}}
          />
          <Text style={[styles.footerText, { fontSize: 12, textAlign: 'center', margin: 5 }]}>
            We are not an emergency response service. If your life is in danger or you are in need of urgent medical care, please call 999.
            </Text>
            </View>
          <Text style={[styles.footerLinkTextBottom]}>
            By continuing, you agree to our{' '}
            <Text onPress={() => navigation.navigate('TermsAndConditions')} style={{ textDecorationLine: 'underline', borderBottomColor: '#ffffff', borderBottomWidth: 1 }}>
              Terms & Conditions.
            </Text>
          </Text>
        
        </View> */}
         <View style={styles.footerView}>
          <Text style={[styles.footerText]}>
            Already signed up?{' '}
            <Text
              onPress={() => navigation.navigate('Login')}
              style={styles.footerlinkText}>
              Login
            </Text>


          </Text>
          <View style={{flexDirection:"row",width:'85%'}}>
          <View style={{ alignSelf:"flex-start"}}>
            <CheckBox
            disabled={false}
            value={toggleCheckBox}
            onValueChange={(newValue) => setToggleCheckBox(newValue)}
            tintColors={{true: '#228994'}}
          />
          </View>
          <View style={{textAlign:"center",margin:5,paddingRight:15}}>
          <Text style={[styles.footerText, { fontSize: 12, textAlign: 'center',  }]}>
            We are not an emergency response service,If your life is in danger
            or you are in need of urgent medical care,please call 999.
            </Text>
            </View>
          </View>
          <Text style={[styles.footerLinkTextBottom]}>
            By continuing, you agree to our{' '}
            <Text onPress={() => navigation.navigate('TermsAndConditions')} style={{ textDecorationLine: 'underline', borderBottomColor: '#ffffff', borderBottomWidth: 1 }}>
              Terms & Conditions.
            </Text>
          </Text>

        </View> 


      </ScrollView>

      <AwesomeAlert
        show={showRoles}
        closeOnTouchOutside={true}
        onConfirmPressed={() => {
          setShowRoles(false);
        }}
        onDismiss={() => {
          setShowRoles(false);
        }}
        customView={<Roles />}
      />

      <AwesomeAlert
        show={showLanguages}
        closeOnTouchOutside={true}
        onConfirmPressed={() => {
          setShowLanguages(false);
        }}
        onDismiss={() => {
          setShowLanguages(false);
        }}
        customView={<ListView />}
      />
      <AwesomeAlert
        show={showSpecialism}
        closeOnTouchOutside={true}
        onConfirmPressed={() => {
          setShowSpecialism(false);
        }}
        onDismiss={() => {
          setShowSpecialism(false);
        }}
        customView={<SpecialitiesList />}
      />

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
    // </KeyboardAwareScrollView>
  );
};
export default connect()(SignUp);
