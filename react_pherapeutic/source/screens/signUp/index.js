import React, {useEffect, useState} from 'react';
import {
  View,
  Text,
  StyleSheet,
  Dimensions,
  Image,
  TextInput,
  ScrollView,
  KeyboardAvoidingView,
  TouchableOpacity,
  Modal,
} from 'react-native';
import styles from './styles';
import {KeyboardAwareScrollView} from 'react-native-keyboard-aware-scroll-view';
import {validateEmail} from '../../utils/validateStrings';
//import {TouchableOpacity} from 'react-native-gesture-handler';
import AwesomeAlert from 'react-native-awesome-alerts';
import Geolocation from '@react-native-community/geolocation';
import constants from '../../utils/constants';
import Events from '../../utils/events';
import APICaller from '../../utils/APICaller';
import SubmitButton from '../../components/submitButton';
import AsyncStorage from '@react-native-async-storage/async-storage';
import {saveUser} from '../../redux/actions/user';
import {connect} from 'react-redux';
import CheckBox from '@react-native-community/checkbox';
import Scale from '../../utils/constants/Scale';
import MaterialIcons from 'react-native-vector-icons/MaterialIcons';

const {height, width} = Dimensions.get('window');
var language_select_var = [];
var specility_select_var = [];
var qualification_select_var = [];
const SignUp = (props) => {
  const [first_name, setFirstName] = useState('');
  const [last_name, setLastName] = useState('');
  const [email, setEmail] = useState('');
  const [emailError, setEmailError] = useState('');
  const [password, setPassword] = useState('');
  const [confirm_password, setConfirmPassword] = useState('');
  const [role, setRole] = useState('');
  const [language, setLanguage] = useState('');
  const [language_id, setLanguage_id] = useState([]);
  const [showRoles, setShowRoles] = useState(false);
  const [showLanguages, setShowLanguages] = useState(false);
  const [showSpecialism, setShowSpecialism] = useState(false);
  const [selectedSpeciality, setSpeciality] = useState('');
  const [speciality_id, setSpeciality_id] = useState([]);
  const [years, setYears] = useState('');
  const [languages, storeLanguages] = useState([]);
  const [location, storeLocation] = useState([]);
  const [specialities, storeSpecialities] = useState([]);
  const [showAlert, setShowAlert] = useState(false);
  const [alertMessage, setAlert] = useState('Please Fill All Fields');
  const [readyToSubmit, setReadyToSibmit] = useState(true);
  const [token, setToken] = useState(null);
  const [toggleCheckBox, setToggleCheckBox] = useState(false);
  const [visible, setVisible] = useState(false);
  const [showQualification, setShowQaulification] = useState(false);
  const [qualification, setQualification] = useState([]);
  const [selectedqualification, setSelectqualification] = useState('');
  const [qualification_id, setQualification_id] = useState([]);
  const [qualifications, storeQualification] = useState([])
  const {navigation, route, dispatch} = props;
  const {params} = route;

  // const {params} = props.navigation.state;
  console.log('params', params);
  useEffect(() => {
    console.log(specility_select_var);
    language_select_var = [];
    specility_select_var = [];
    qualification_select_var = [];
    getCurrentCoordinates();
    getLanguages();
    getSpecialities();
    getQualifications();
    console.log('useeffect state', showAlert);

    if (params && (params.fbtoken || params.googletoken || params.appletoken)) {
      console.log(params)
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
        const {data, message, status, statusCode} = response['data'];
        if (status === 'success') {
          storeLanguages([...data]);
        }
      })
      .catch((error) => {
        console.log('error getting languages => ', error);
      });
  };
  const addQualificationClick = (id, title) => {
    console.log('id',id)
    console.log('title',title)
    if (qualification_select_var.find((data) => data == title)) {
      let filteredArray2 = qualification_select_var.filter(
        (item) => item !== title,
      );
      qualification_select_var = filteredArray2;
      const commaSepLang2 = filteredArray2.map((item) => item).join(', ');
      setSelectqualification(commaSepLang2);
    } else {
      qualification_select_var.push(title);
      const commaSepLang3 = qualification_select_var.map((item) => item).join(', ');
      console.log('commaSepLang3',commaSepLang3)
      setSelectqualification(commaSepLang3);
    }
    if (qualification_id.find((data) => data == id)) {
      let filteredArray = qualification_id.filter((item) => item !== id);
      setQualification_id(filteredArray);
    } else {
      setQualification_id([...qualification_id, id]);
    }
  };
  const getCurrentCoordinates = async () => {
    await Geolocation.getCurrentPosition((info) => storeLocation(info));
    console.log('location is', location);
  };

  const getSpecialities = () => {
    APICaller('getTherapistTypes', 'GET')
      .then((response) => {
        console.log('response getting specialiites => ', response);
        const {data, message, status, statusCode} = response['data'];
        if (status === 'success') {
          storeSpecialities([...data]);
        }
      })
      .catch((error) => {
        console.log('error getting specialiites => ', error);
      });
  };
  const getQualifications = () => {
    APICaller('getQualification', 'GET')
      .then((response) => {
        console.log('response getting getQualifications => ', response);
        const {data, message, status, statusCode} = response['data'];
        if (status === 'success') {
          storeQualification([...data]);
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

    console.log('lattitude and longitude', latitude, longitude);
    console.log('fcm token', token);
    //   var registerObj = {
    //     first_name,
    //     last_name,
    //     email,
    //     password,
    //     confirm_password,
    //     role: role == 'Client' ? '0' : '1',
    //    // languages: [language_id],
    //     experience: years,
    //     specialism: [speciality_id],
    //     device_type: '1',
    //     fcm_token: token,
    //     qualification: 'Bachelor Degree',
    //     address: 'ABC place',
    //     latitude: latitude,
    //     longitude: longitude,
    //   };
    //   for (var i = 0; i < language_id.length; i++) {
    //     let value = language_id[i]
    //     registerObj.languages[i] = value;
    //     console.log(registerObj)
    //  //   formData.append('languages[' + i + ']', value);
    //   }

    const formData = new FormData();
    formData.append('first_name', first_name);
    formData.append('last_name', last_name);
    formData.append('email', email);
    formData.append('password', password);
    formData.append('confirm_password', confirm_password);
    formData.append('role', role == 'Client' ? '0' : '1');
    for (var i = 0; i < language_id.length; i++) {
      let value = language_id[i];
      formData.append('languages[' + i + ']', value);
    }
    for (var k = 0; k < speciality_id.length; k++) {
      let value22 = speciality_id[k];
      formData.append('specialism[' + k + ']', value22);
    }
    formData.append('device_type', 1);
    formData.append('fcm_token', token);
    formData.append('experience', years);
   // formData.append('qualification', 'Bachelor Degree');
    for (var k1 = 0; k1 < qualification_id.length; k1++) {
      let value223 = qualification_id[k1];
      console.log(value223)
      formData.append('qualification[' + k1 + ']', value223);
    }
    formData.append('address', 'ABC place');
    formData.append('latitude', latitude);
    formData.append('longitude', longitude);

    if (params && (params.fbtoken || params.googletoken || params.appletoken)) {
      // registerObj.image = params.image;
      formData.append('image', params.image);
      if (params.fbtoken) {
        //registerObj.social_token = params.fbtoken;
        formData.append('social_token', params.fbtoken);
        //  registerObj.login_type = 2;
        formData.append('login_type', 2);
      } else if (params.googletoken) {
        formData.append('social_token', params.googletoken);
        formData.append('login_type', 1);
        //  registerObj.social_token = params.googletoken;
        //  registerObj.login_type = 1;
      } else if (params.appletoken) {
        formData.append('social_token', params.appletoken);
        formData.append('login_type', 3);
        //  registerObj.social_token = params.appletoken;
        //  registerObj.login_type = 3;
      }

      // only for testing purpose
      // registerObj.social_token = '867672634855435435633847';
      // registerObj.email = 'abcd454837554544@gmail.com';
      // registerObj.login_type = 2;
    }
    console.log('register object => ', formData);
    // alert(JSON.stringify(registerObj));
    APICaller(
      params && (params.fbtoken || params.googletoken || params.appletoken)
        ? 'socialLogin'
        : 'register',
      'POST',
      formData,
    )
      .then((response) => {
        Events.trigger('hideModalLoader');
       // console.warn('response after register => ', response);
        const {data, message, status, statusCode} = response['data'];
        // const { message } = data;

        if (
          params &&
          (params.fbtoken || params.googletoken || params.appletoken)
        ) {
          AsyncStorage.setItem('userData', JSON.stringify(data));
          dispatch(saveUser(data));
          navigation.navigate('Onboarding');
        } else if (message === 'Your account created successfully.') {
          navigation.navigate('VerifyEmail', {user_id: data['user_id']});
        }
      })
      .catch((error) => {
       console.log('error after register => ', error);
        const {data} = error;
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
        toggleCheckBox &&
        first_name &&
        last_name &&
        email &&
        password &&
        confirm_password &&
        role &&
        language_id &&
        password === confirm_password &&
        confirm_password.length >= 6 &&
        password.length >= 6
      ) {
        if (role == 'Therapist') {
          if (!years) {
            setAlert('Please Enter Total Experience.');
            setShowAlert(true);
          } else if (!selectedSpeciality) {
            setAlert('Please Select Speciality');
            setShowAlert(true);
          } else if (!first_name) {
            setAlert('Please Enter First Name.');
            setShowAlert(true);
          } else if (!last_name) {
            setAlert('Please Enter Last Name.');
            setShowAlert(true);
          } else if (!email) {
            setAlert('Please Enter Email Address.');
            setShowAlert(true);
          }
          //else if (emailError) { setAlert('Please Enter Valid Email Address.'); setShowAlert(true); }
          else if (!password) {
            setAlert('Please Enter Password.');
            setShowAlert(true);
          } else if (!confirm_password) {
            setAlert('Please Enter Confirm Password.');
            setShowAlert(true);
          } else if (confirm_password !== password) {
            setAlert('Please Enter Same Password.');
            setShowAlert(true);
          } else if (!role) {
            setAlert('Please Select Your Role.');
            setShowAlert(true);
          } else if (!language) {
            setAlert('Please Select Your Language.');
            setShowAlert(true);
          } else if (password.length < 6) {
            setAlert('Please Fill password at least 6 digit.');
            setShowAlert(true);
          } else if (confirm_password.length < 6) {
            setAlert('Please Fill password at least 6 digit.');
            setShowAlert(true);
          } else if (!toggleCheckBox) {
            setAlert('Please check terms and conditions.');
            setShowAlert(true);
          } else if (validateEmail(email)) {
            setAlert('Please enter valid email');
            setShowAlert(true);
          } else {
            // call api
            registerUser();
          }
        } else {
          // call api
          if (!first_name) {
            setAlert('Please Enter First Name.');
            setShowAlert(true);
          } else if (!last_name) {
            setAlert('Please Enter Last Name.');
            setShowAlert(true);
          } else if (!email) {
            setAlert('Please Enter Email Address.');
            setShowAlert(true);
          }
          // else if (emailError) { setAlert('Please Enter Valid Email Address.'); setShowAlert(true); }
          else if (!password) {
            setAlert('Please Enter Password.');
            setShowAlert(true);
          } else if (!confirm_password) {
            setAlert('Please Enter Confirm Password.');
            setShowAlert(true);
          } else if (confirm_password !== password) {
            setAlert('Please Enter Same Password.');
            setShowAlert(true);
          } else if (!role) {
            setAlert('Please Select Your Role.');
            setShowAlert(true);
          } else if (!language) {
            setAlert('Please Select Your Language.');
            setShowAlert(true);
          } else if (password.length < 6) {
            setAlert('Please Fill password at least 6 digit.');
            setShowAlert(true);
          } else if (confirm_password.length < 6) {
            setAlert('Please Fill password at least 6 digit.');
            setShowAlert(true);
          } else if (!toggleCheckBox) {
            setAlert('Please check terms and conditions.');
            setShowAlert(true);
          } else if (validateEmail(email)) {
            setAlert('Please enter valid email');
            setShowAlert(true);
          } else {
            registerUser();
          }
        }
      } else if (params && params.appletoken) {
        if (!role) {
          setAlert('Please Select Your Role.');
          setShowAlert(true);
        } else if (!language) {
          setAlert('Please Select Your Language.');
          setShowAlert(true);
        } else if (!toggleCheckBox) {
          setAlert('Please check terms and conditions.');
          setShowAlert(true);
        } else {
          registerUser();
        }
      } else {
        if (!first_name) setAlert('Please Enter First Name.');
        else if (!last_name) setAlert('Please Enter Last Name.');
        else if (!email) setAlert('Please Enter Email Address.');
        // else if (emailError) setAlert('Please Enter Valid Email Address.');
        else if (!password) setAlert('Please Enter Password.');
        else if (!confirm_password) setAlert('Please Enter Confirm Password.');
        else if (confirm_password !== password)
          setAlert('Please Enter Same Password.');
        else if (!role) setAlert('Please Select Your Role.');
        else if (!language) setAlert('Please Select Your Language.');
        else if (password.length < 6)
          setAlert('Please Fill password at least 6 digit.');
        else if (confirm_password.length < 6)
          setAlert('Please Fill password at least 6 digit.');
        else if (!toggleCheckBox)
          setAlert('Please check terms and conditions.');
        else if (validateEmail(email)) setAlert('Please enter valid email');
        setShowAlert(true);

        //setShowAlert(!showAlert)
      }
    } else {
      if (
        toggleCheckBox &&
        first_name &&
        last_name &&
        // email &&
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
        //  else if (!email) setAlert('Please Enter Email Address.');
        //  else if (emailError) setAlert('Please Enter Valid Email Address.');
        else if (!role) setAlert('Please Select Your Role.');
        else if (!language) setAlert('Please Select Your Language.');
        else if (!toggleCheckBox)
          setAlert('Please check terms and conditions.');
        setShowAlert(true);
      }
    }
  };

  // const ListView = () => (
  //   <ScrollView>
  //     {/* <View style={{justifyContent: 'center', alignItems: 'center'}}> */}
  //     {languages.map((lang) => {
  //       console.log('!!!!!!!!!!');
  //       return (
  //         <TouchableOpacity
  //           onPress={() => {
  //             setLanguage_id(lang['id']);
  //             setLanguage(lang['title']);
  //             setShowLanguages(false);
  //           }}
  //           style={{
  //             height: Dimensions.get('window').height * 0.05,
  //             width: Dimensions.get('window').width * 0.6,
  //             paddingHorizontal: 2,
  //             justifyContent: 'center',
  //             alignItems: 'flex-start',
  //           }}>
  //           <Text>{lang['title']}</Text>
  //         </TouchableOpacity>
  //       );
  //     })}
  //     {/* </View> */}
  //   </ScrollView>
  // );
  const addLanguageClick = (id, title) => {
    if (language_select_var.find((data) => data == title)) {
      let filteredArray2 = language_select_var.filter((item) => item !== title);
      language_select_var = filteredArray2;
      const commaSepLang2 = filteredArray2.map((item) => item).join(', ');
      setLanguage(commaSepLang2);
    } else {
      language_select_var.push(title);
      const commaSepLang3 = language_select_var.map((item) => item).join(', ');
      setLanguage(commaSepLang3);
    }
    if (language_id.find((data) => data == id)) {
      let filteredArray = language_id.filter((item) => item !== id);
      setLanguage_id(filteredArray);
    } else {
      setLanguage_id([...language_id, id]);
    }
  };

  const addSpecilismClick = (id, title) => {
    console.log(specility_select_var);
    if (specility_select_var.find((data) => data == title)) {
      let filteredArray2 = specility_select_var.filter(
        (item) => item !== title,
      );
      specility_select_var = filteredArray2;
      const commaSepLang2 = filteredArray2.map((item) => item).join(', ');
      setSpeciality(commaSepLang2);
    } else {
      specility_select_var.push(title);
      const commaSepLang3 = specility_select_var.map((item) => item).join(', ');
      setSpeciality(commaSepLang3);
    }
    if (speciality_id.find((data) => data == id)) {
      let filteredArray = speciality_id.filter((item) => item !== id);
      setSpeciality_id(filteredArray);
    } else {
      setSpeciality_id([...speciality_id, id]);
    }
  };

  const SpecialitiesList = () => (
    <View style={{justifyContent: 'center', alignItems: 'center'}}>
      {specialities.map((speciality) => {
        //console.log('!!!!!!!!!!');
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
    <View style={{justifyContent: 'center', alignItems: 'center'}}>
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
    <View style={{flex: 1}}>
      <Image
        source={constants.images.background}
        resizeMode={'stretch'}
        style={styles.containerBackground}
      />

      <ScrollView
        showsVerticalScrollIndicator={false}
        bounces={false}
        contentContainerStyle={styles.container}>
        <Image
          source={constants.images.formsBackground}
          resizeMode={'stretch'}
          style={[
            styles.formsBackground,
            {
              height: height / 2,
            },
          ]}
        />
        <View style={[styles.backButtonView, {}]}>
          <View
            style={{
              flexDirection: 'row',
              justifyContent: 'space-between',
              alignItems: 'center',
              width: '75%',
            }}>
            <TouchableOpacity
              onPress={() => navigation.goBack()}
              style={{
                flex: 1.5,

                justifyContent: 'center',
                alignItems: 'center',
              }}>
              <Image
                source={constants.images.backIcon}
                style={{
                  height: 20,
                  width: 10,
                  margin: 10,
                }}
              />
            </TouchableOpacity>
            <View
              style={{
                //flex: 7,
                justifyContent: 'center',
                alignItems: 'center',
                top: 0,
              }}>
              <Image
                source={constants.images.logo}
                resizeMode={'contain'}
                style={{
                  height: Dimensions.get('window').height * 0.07,
                  width: Dimensions.get('window').width * 0.5,
                }}
              />
            </View>
          </View>
          <View style={{flex: 1.5}} />
        </View>

        <View style={styles.formView}>
          <View style={styles.formWrap}>
            {!(
              params &&
              (params.appletoken || params.googletoken || params.fbtoken)
            ) && (
              <View style={styles.formField}>
                <Text style={styles.fieldName}>FIRST NAME</Text>
                <View style={styles.fieldInputWrap}>
                  <TextInput
                    style={styles.fieldInput}
                    onChangeText={(text) =>
                      setFirstName(
                        text.replace(
                          /[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi,
                          '',
                        ),
                      )
                    }
                    value={first_name}
                    autoCompleteType={'off'}
                    autoCorrect={false}
                    editable={
                      !(params && (params.fbtoken || params.googletoken))
                    }
                    onFocus={() => getCurrentCoordinates()}
                  />
                </View>
              </View>
            )}
            {!(
              params &&
              (params.appletoken || params.googletoken || params.fbtoken)
            ) && (
              <View style={styles.formField}>
                <Text style={styles.fieldName}>LAST NAME</Text>
                <View style={styles.fieldInputWrap}>
                  <TextInput
                    style={styles.fieldInput}
                    onChangeText={(text) =>
                      setLastName(
                        text.replace(
                          /[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi,
                          '',
                        ),
                      )
                    }
                    value={last_name}
                    editable={
                      !(params && (params.fbtoken || params.googletoken))
                    }
                    autoCompleteType={'off'}
                    autoCorrect={false}
                  />
                </View>
              </View>
            )}
            {!(
              params &&
              (params.appletoken || params.googletoken || params.fbtoken)
            ) && (
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
            )}
            {!(
              params &&
              (params.appletoken || params.googletoken || params.fbtoken)
            ) && (
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
            {!(
              params &&
              (params.appletoken || params.googletoken || params.fbtoken)
            ) && (
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
                    {width: Dimensions.get('window').width * 0.7},
                  ]}
                  onChangeText={(text) => setLanguage(text)}
                  value={role}
                  editable={false}
                  autoCompleteType={'off'}
                  autoCorrect={false}
                />
                <TouchableOpacity onPress={() => setShowRoles(true)}>
                  <Image
                    style={{height: 25, width: 25, margin: 3}}
                    source={constants.images.downArrow}
                  />
                </TouchableOpacity>
              </View>
            </View>

            {role == 'Therapist' ? (
              <View style={{justifyContent: 'center', alignItems: 'center'}}>
                <View style={styles.formField}>
                  <Text style={styles.fieldName}>SPECIALISM</Text>
                  <ScrollView>
                    <View
                      style={{
                        minHeight: 40,
                        width: width * 0.8,
                        backgroundColor: constants.colors.white,
                        borderRadius: 4,
                        justifyContent: 'center',
                        alignItems: 'center',
                        flexDirection: 'row',
                      }}>
                      <Text
                        style={{
                          color: '#939393',
                          width: width * 0.7,
                          fontSize: 14,
                          fontWeight: '500',
                          paddingLeft: 5,
                        }}>
                        {selectedSpeciality}
                      </Text>

                      <TouchableOpacity onPress={() => setShowSpecialism(true)}>
                        <Image
                          style={{height: 25, width: 25, margin: 3}}
                          source={constants.images.downArrow}
                        />
                      </TouchableOpacity>
                    </View>
                  </ScrollView>
                </View>
                <View style={styles.formField}>
                  <Text style={styles.fieldName}>QUALIFICATIONS</Text>
                  <ScrollView>
                    <View
                      style={{
                        minHeight: 40,
                        width: width * 0.8,
                        backgroundColor: constants.colors.white,
                        borderRadius: 4,
                        justifyContent: 'center',
                        alignItems: 'center',
                        flexDirection: 'row',
                      }}>
                      <Text
                        style={{
                          color: '#939393',
                          width: width * 0.7,
                          fontSize: 14,
                          fontWeight: '500',
                          paddingLeft: 5,
                        }}>
                        {selectedqualification}
                      </Text>

                      <TouchableOpacity onPress={() => setShowQaulification(true)}>
                        <Image
                          style={{height: 25, width: 25, margin: 3}}
                          source={constants.images.downArrow}
                        />
                      </TouchableOpacity>
                    </View>
                  </ScrollView>
                </View>

                <View style={styles.formField}>
                  <Text style={styles.fieldName}>YEARS OF EXPERIENCE</Text>
                  <View style={styles.fieldInputWrap}>
                    <TextInput
                      style={styles.fieldInput}
                      onChangeText={(text) =>
                        setYears(text.replace(/[^0-9]/g, ''))
                      }
                      value={years}
                      autoCompleteType={'off'}
                      autoCorrect={false}
                      maxLength={2}
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
              <ScrollView>
                <View
                  style={{
                    minHeight: 40,
                    width: width * 0.8,
                    backgroundColor: constants.colors.white,
                    //  backgroundColor:'red',
                    borderRadius: 4,
                    justifyContent: 'center',
                    alignItems: 'center',
                    flexDirection: 'row',
                  }}>
                  <Text
                    style={{
                      color: '#939393',
                      width: width * 0.7,
                      fontSize: 14,
                      fontWeight: '500',
                      paddingLeft: 10,
                    }}>
                    {language}
                  </Text>

                  <TouchableOpacity onPress={() => setShowLanguages(true)}>
                    <Image
                      style={{height: 25, width: 25, margin: 3}}
                      source={constants.images.downArrow}
                    />
                  </TouchableOpacity>
                </View>
              </ScrollView>
            </View>

            <SubmitButton
              title={
                !(
                  params &&
                  (params.appletoken || params.googletoken || params.fbtoken)
                )
                  ? 'SIGN UP'
                  : 'Continue'
              }
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
      </ScrollView>
      <View
        style={{
          //height: '12%',
          height: Scale.moderateScale(115),

          flexDirection: 'column',
          justifyContent: 'space-between',
          alignItems: 'center',
          position: 'relative', //Here is the trick
          //marginTop: 20,
          margin: 10,
          bottom: 10, //Here is the trick
          marginHorizontal: Scale.moderateScale(10),
        }}>
        {!(
          params &&
          (params.appletoken || params.googletoken || params.fbtoken)
        ) && (
          <>
            <Text
              style={[
                styles.footerText,
                {
                  textAlign: 'center',
                },
              ]}>
              Already signed up?{' '}
              <Text
                onPress={() => navigation.navigate('Login')}
                style={styles.footerlinkText}>
                Login
              </Text>
            </Text>
          </>
        )}

        <View
          style={{
            //  flex: 0.8,

            width: width / 1.1,

            flexDirection: 'row',
            // alignItems: 'center',
            justifyContent: 'space-between',
          }}>
          {/* <CheckBox
            disabled={false}
            value={toggleCheckBox}
            onValueChange={(newValue) => setToggleCheckBox(newValue)}
            tintColors={{true: '#228994'}}

            // style={{transform: [{scaleX: 0.8}, {scaleY: 0.7}]}}
          /> */}
          <TouchableOpacity
            onPress={() => setToggleCheckBox(!toggleCheckBox)}
            style={{left: 15}}>
            {toggleCheckBox ? (
              <MaterialIcons
                size={30}
                name="radio-button-checked"
                color={'white'}
              />
            ) : (
              <MaterialIcons
                size={30}
                name="radio-button-unchecked"
                color={'white'}
              />
            )}
          </TouchableOpacity>

          <Text
            style={[
              styles.footerText,
              {
                fontSize: 13,
                //alignSelf: 'center',
                width: width / 1.3,
                marginLeft: 10,
                textAlign: 'center',
              },
            ]}>
            We are not an emergency response service,If your life is in danger
            or you are in need of urgent medical care,please call 999.
          </Text>
        </View>
        <Text
          style={[
            styles.footerLinkTextBottom,
            {
              alignItems: 'center',
              justifyContent: 'center',
              textAlign: 'center',
            },
          ]}>
          By continuing, you agree to our{' '}
          <Text
            onPress={() => navigation.navigate('TermsAndConditions')}
            style={{
              textDecorationLine: 'underline',
              borderBottomColor: '#ffffff',
              borderBottomWidth: 1,
            }}>
            Terms & Conditions.
          </Text>
        </Text>
      </View>

      {/* <View
          style={{
            flexDirection: 'column',
            justifyContent: 'space-between',

            height: Dimensions.get('window').height / 4.5,
            margin: 10,
            alignItems: 'center',
          }}>
          <View style={{}}>
            {!(params && params.appletoken) && (
              <>
                <Text style={[styles.footerText]}>
                  Already signed up?{' '}
                  <Text
                    onPress={() => navigation.navigate('Login')}
                    style={styles.footerlinkText}>
                    Login
                  </Text>
                </Text>
              </>
            )}
          </View>

          <View
            style={{
              flexDirection: 'row',

              width: width * 0.95,
              justifyContent: 'space-between',

              flex: 0.6,
              //height: height * 0.1,
              alignItems: 'center',
            }}>
            <View
              style={
                {
                  //alignSelf: 'flex-start',
                }
              }>
              <CheckBox
                disabled={false}
                value={toggleCheckBox}
                onValueChange={(newValue) => setToggleCheckBox(newValue)}
                tintColors={{true: '#228994'}}
              />
            </View>
            <View
              style={{
                // width: width * 0.8,
                // height: height * 0.1,
                justifyContent: 'center',
                flex: 0.9,
                alignItems: 'center',
                //backgroundColor: 'red',
              }}>
              <Text
                style={[
                  styles.footerText,
                  {
                    fontSize: 12,
                    alignSelf: 'center',

                    //alignItems: 'center',
                  },
                ]}>
                We are not an emergency response service,If your life is in
                danger or you are in need of urgent medical care,please call
                999.
              </Text>
            </View>
          </View>

          <View
            style={{
              justifyContent: 'center',
              alignItems: 'center',
              //bottom: 20,
              //marginTop: 5,
            }}>
            <Text
              style={[
                styles.footerLinkTextBottom,
                {
                  alignItems: 'center',
                  justifyContent: 'center',
                },
              ]}>
              By continuing, you agree to our{' '}
              <Text
                onPress={() => navigation.navigate('TermsAndConditions')}
                style={{
                  textDecorationLine: 'underline',
                  borderBottomColor: '#ffffff',
                  borderBottomWidth: 1,
                }}>
                Terms & Conditions.
              </Text>
            </Text>
          </View>
        </View> */}

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
      <Modal
        animationType="slide"
        transparent={true}
        visible={showLanguages}
        onRequestClose={() => {
          setShowLanguages(!showLanguages);
        }}>
        <Image
          source={constants.images.background}
          resizeMode={'stretch'}
          style={styles.containerBackground}
        />
        <View style={styles.centeredView}>
          <View style={styles.modalView}>
            <View style={{flexDirection: 'row', height: 50}}>
              <TouchableOpacity
                onPress={() => setShowLanguages(false)}
                style={{
                  flex: 0.8,
                }}>
                <Image
                  source={constants.images.backArrowWhite}
                  style={{height: 18, width: 10, margin: 10}}
                />
              </TouchableOpacity>
              <View style={{flex: 1.6}}>
                <Text style={styles.modalText}>Languages </Text>
              </View>
            </View>
            <ScrollView style={{width: '100%'}}>
              {languages.map((lang) => {
                return (
                  <View
                    style={{
                      flexDirection: 'row',
                      flex: 1,
                      borderColor: '#228994',
                      borderWidth: 1,
                      marginLeft: 20,
                      marginRight: 20,
                      marginBottom: 10,
                    }}>
                    <TouchableOpacity
                      onPress={() => {
                        addLanguageClick(lang['id'], lang['title']);
                      }}
                      style={{
                        height: Dimensions.get('window').height * 0.05,
                        // width: Dimensions.get('window').width * 0.6,
                        paddingHorizontal: 2,
                        //  justifyContent: 'center',
                        alignItems: 'center',
                        alignContent: 'center',
                        flexDirection: 'row',
                        flex: 1,
                        // margin:10

                        marginLeft: 5,
                      }}>
                      {language_id.find((data) => data == lang['id']) ? (
                        <Image source={constants.images.Selectedimg} />
                      ) : (
                        <Image source={constants.images.Non_Selectedimg} />
                      )}
                      <Text style={{color: 'white', paddingLeft: 15}}>
                        {lang['title']}{' '}
                      </Text>
                    </TouchableOpacity>
                  </View>
                );
              })}
            </ScrollView>
          </View>
        </View>
      </Modal>
      {/* <AwesomeAlert
        show={showLanguages}
        closeOnTouchOutside={true}
        onConfirmPressed={() => {
          setShowLanguages(false);
        }}
        onDismiss={() => {
          setShowLanguages(false);
        }}
        customView={<ListView />}
      /> */}
      {/* <AwesomeAlert
        show={showSpecialism}
        closeOnTouchOutside={true}
        onConfirmPressed={() => {
          setShowSpecialism(false);
        }}
        onDismiss={() => {
          setShowSpecialism(false);
        }}
        customView={<SpecialitiesList />}
      /> */}

      <Modal
        animationType="slide"
        transparent={true}
        visible={showSpecialism}
        onRequestClose={() => {
          setShowSpecialism(!showSpecialism);
        }}>
        <Image
          source={constants.images.background}
          resizeMode={'stretch'}
          style={styles.containerBackground}
        />
        <View style={styles.centeredView}>
          <View style={styles.modalView}>
            <View style={{flexDirection: 'row', height: 50}}>
              <TouchableOpacity
                onPress={() => setShowSpecialism(false)}
                style={{
                  flex: 0.8,
                }}>
                <Image
                  source={constants.images.backArrowWhite}
                  style={{height: 18, width: 10, margin: 10}}
                />
              </TouchableOpacity>
              <View style={{flex: 1.6}}>
                <Text style={styles.modalText}>Specialism </Text>
              </View>
            </View>
            <View>
              <Text style={{paddingBottom: 5, color: 'white', fontSize: 18}}>
                Select the areas you specialise in
              </Text>
            </View>
            <ScrollView style={{width: '100%'}}>
              {specialities.map((speciality) => {
                return (
                  <View
                    style={{
                      flexDirection: 'row',
                      flex: 1,
                      borderColor: '#228994',
                      borderWidth: 1,
                      marginLeft: 20,
                      marginRight: 20,
                      marginBottom: 10,
                    }}>
                    <TouchableOpacity
                      onPress={() => {
                        addSpecilismClick(
                          speciality['id'],
                          speciality['title'],
                        );
                      }}
                      style={{
                        height: Dimensions.get('window').height * 0.05,
                        // width: Dimensions.get('window').width * 0.6,
                        paddingHorizontal: 2,
                        //  justifyContent: 'center',
                        alignItems: 'center',
                        alignContent: 'center',
                        flexDirection: 'row',
                        flex: 1,
                        // margin:10

                        marginLeft: 5,
                      }}>
                      {speciality_id.find(
                        (data) => data == speciality['id'],
                      ) ? (
                        <Image source={constants.images.Selectedimg} />
                      ) : (
                        <Image source={constants.images.Non_Selectedimg} />
                      )}
                      <Text style={{color: 'white', paddingLeft: 15}}>
                        {speciality['title']}{' '}
                      </Text>
                    </TouchableOpacity>
                  </View>
                );
              })}
            </ScrollView>
          </View>
        </View>
      </Modal>

       {/* Multiple qualification */}
    <Modal
        animationType="slide"
        transparent={true}
        visible={showQualification}
        onRequestClose={() => {
          setShowQaulification(!showQualification);
        }}>
        <Image
          source={constants.images.background}
          resizeMode={'stretch'}
          style={styles.containerBackground}
        />
        <View style={styles.centeredView}>
          <View style={styles.modalView}>
            <View style={{flexDirection: 'row', height: 50}}>
              <TouchableOpacity
                onPress={() => setShowQaulification(false)}
                style={{
                  flex: 0.8,
                }}>
                <Image
                  source={constants.images.backArrowWhite}
                  style={{height: 18, width: 10, margin: 10}}
                />
              </TouchableOpacity>
              <View style={{flex: 1.6}}>
                <Text style={styles.modalText}>Qualification </Text>
              </View>
            </View>
            <ScrollView style={{width: '100%'}}>
              {qualifications.map((quali) => {
                return (
                  <View
                    style={{
                      flexDirection: 'row',
                      flex: 1,
                      borderColor: '#228994',
                      borderWidth: 1,
                      marginLeft: 20,
                      marginRight: 20,
                      marginBottom: 10,
                    }}>
                    <TouchableOpacity
                      onPress={() => {
                        addQualificationClick(quali['id'], quali['title']);
                      }}
                      style={{
                        height: Dimensions.get('window').height * 0.05,
                        paddingHorizontal: 2,
                        alignItems: 'center',
                        alignContent: 'center',
                        flexDirection: 'row',
                        flex: 1,
                        marginLeft: 5,
                      }}>
                      { qualification_id.length>0 && qualification_id.find((data) => data == quali['id']) ? (
                        <Image source={constants.images.Selectedimg} />
                      ) : (
                        <Image source={constants.images.Non_Selectedimg} />
                      )}
                      <Text style={{color: 'white', paddingLeft: 15}}>
                        {quali['title']}{' '}
                      </Text>
                    </TouchableOpacity>
                  </View>
                );
              })}
            </ScrollView>
          </View>
        </View>
      </Modal>
    {/* end */}

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
const mapStateToProps = (state) => ({
  userData: state.user.userData,
});
export default connect(mapStateToProps)(SignUp);
