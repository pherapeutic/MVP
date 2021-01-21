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
} from 'react-native';
import constants from '../../utils/constants';
import styles from './styles';
import LogoutAlert from '../../components/logoutAlert';
import { connect } from 'react-redux';
import APICaller from '../../utils/APICaller';
import { saveUserProfile } from '../../redux/actions/user';
import SubmitButton from '../../components/submitButton';
import AsyncStorage from '@react-native-async-storage/async-storage';
import ImagePicker from 'react-native-image-crop-picker';
import AwesomeAlert from 'react-native-awesome-alerts';
import { validateEmail } from '../../utils/validateStrings';
import { KeyboardAwareScrollView } from 'react-native-keyboard-aware-scroll-view';
import Events from '../../utils/events';
import Header from '../../components/Header';
//import Geolocation from '@react-native-community/geolocation';

const { height, width } = Dimensions.get('window');

const EditProfile = (props) => {
  const [userInfo, setUserInfo] = useState({});
  const [localURL, setLocalURL] = useState('');
  const [imageFile, setImageFile] = useState(null);
  const [languages, storeLanguages] = useState([]);
  const [specialities, storeSpecialities] = useState([]);
  const [first_name, setFirstName] = useState('');
  const [last_name, setLastName] = useState('');
  const [email, setEmail] = useState('');
  const [emailError, setEmailError] = useState('');
  const [language, setLanguage] = useState('');
  const [language_id, setLanguage_id] = useState();
  const [showLanguages, setShowLanguages] = useState(false);
  const [showSpecialism, setShowSpecialism] = useState(false);
  const [selectedSpeciality, setSpeciality] = useState('');
  const [speciality_id, setSpeciality_id] = useState('');
  const [years, setYears] = useState('');
  const [showAlert, setShowAlert] = useState(false);
  const [alertMessage, setAlert] = useState('Please Fill All Fields');
  const [readyToSubmit, setReadyToSibmit] = useState(true);
  const [qualification, setQualification] = useState('');
  const [imageURL, setImageURL] = useState(null);
  const [token, setToken] = useState(null);

  const editProfileHandler = () => {
    if (role == 1) {
      if (!first_name || !last_name || !email || !years || !qualification) {
        setAlert('Fields can not be empty.');
        setShowAlert(true);
      } else if (emailError) {
        setAlert('Email is not valid.');
        setShowAlert(true);
      } else {
        const formData = new FormData();
        formData.append('first_name', first_name);
        formData.append('last_name', last_name);
        console.log('language_id', language_id)

        formData.append('languages', language_id);

        formData.append('device_type', 1);

        formData.append('fcm_token', token);
        formData.append('experience', years);
        formData.append('qualification', qualification);
        formData.append('specialism', speciality_id);

        // formData.append('address', 'XYZ place');

        // formData.append('latitude', 23.54);

        // formData.append('longitude', 87.32);

        if (localURL) {
          formData.append('image', {
            uri: localURL, //Your Image File Path
            type: 'image/jpeg',
            // name: imageFile.filename,
            name: Math.random().toString(36).substring(7),
          });

        } else {
          // formData.append('image', image);
        }
        updateProfile(formData);
      }
    } else {
      if (!first_name || !last_name || !email) {
        setAlert('Fields can not be empty.');
        setShowAlert(true);
      } else if (emailError) {
        setAlert('Email is not valid.');
        setShowAlert(true);
      } else {
        // const body = {
        //   first_name,
        //   last_name,
        //   languages: language_id,
        //   device_type: 1,
        //   fcm_token: 'mnvmvnbnmvnbnv',
        //   address: 'XYZ place',
        //   latitude: 23.54,
        //   longitude: 87.32,
        //   image: null,
        // };
        // updateProfile(body);

        const formData = new FormData();
        formData.append('first_name', first_name);
        formData.append('last_name', last_name);
        console.log('language_id', language_id)
        formData.append('languages', language_id);

        formData.append('device_type', 1);

        formData.append('fcm_token', token);
        // formData.append('address', '');

        // formData.append('latitude', 23.54);

        // formData.append('longitude', 87.32);

        if (localURL) {
          formData.append('image', {
            uri: localURL, //Your Image File Path
            type: 'image/jpeg',
            // name: imageFile.filename,
            name: Math.random().toString(36).substring(7),
          });

        } else {
          //formData.append('image', image);
        }
        updateProfile(formData);
      }
    }
  };

  const updateProfile = (body) => {
    Events.trigger('showModalLoader');
    const endpoint = 'user/profile/update';
    const method = 'POST';
    const headers = {
      'Content-Type': 'multipart/form-data',
      Authorization: `Bearer ${userToken}`,
      Accept: 'application/json',
    };

    console.log('data before sending => ', body);

    APICaller(endpoint, method, body, headers)
      .then((response) => {
        //alert(JSON.stringify(response));
        Events.trigger('hideModalLoader');
        console.log('response updating user profile => ', response['data'].data);
        const { status, statusCode, message, data } = response['data'];
        if (status === 'success') {
          dispatch(saveUserProfile(data));
          setAlert('Profile Update Successfully');
          setShowAlert(true);
        }
      })
      .catch((error) => {
        // alert(JSON.stringify(error));

        Events.trigger('hideModalLoader');
        console.log('error updating user profile => ', error['data']);
        const { status, statusCode, message, data } = error['data'];
        setAlert(message);
        setShowAlert(true);
      });
  };
  const { navigation, userData, dispatch, userToken } = props;

  const { image, is_email_verified, role } = userData;

  useEffect(() => {
    setUserInfo(userData);
    getLanguages();
    getSpecialities();
    console.log('user data get => ', userData);
    setFirstName(userData['first_name']);
    setLastName(userData['last_name']);
    setEmail(userData['email']);
    setLanguage(
      userData.languages.length ? userData['languages'][0]['title'] : 'English',
    );
    setLanguage_id(
      userData.languages.length ? userData['languages'][0]['id'] : '1',
    );
    setImageURL(userData['image']);

    if (role == 1) {
      setQualification(userData['qualification']);
      setYears(userData['experience'].toString());
      setSpeciality(userData['specialism'][0]['title']);
      setSpeciality_id(userData['specialism'][0]['id']);
    }
    AsyncStorage.getItem('fcmToken').then(token => {
      setToken(token)
    });

  }, []);

  const uploadImage = () => {
    ImagePicker.openPicker({
      mediaType: 'photo',
    }).then(async (file) => {
      console.log('image response => ', file);
      setLocalURL(file.path);
      setImageFile(file);
      // alert(JSON.stringify(file));
    });
  };

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

  const ListView = () => (
    <ScrollView >
      <View style={{ justifyContent: 'center', alignItems: 'center' }}>
        {languages.map((lang) => {
          return (
            <TouchableOpacity
              onPress={() => {
                //  setLanguage_id([lang['id']]);
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

  return (
    <KeyboardAwareScrollView
      showsVerticalScrollIndicator={false}
      contentContainerStyle={{ width: '100%', flex: 1 }}
      keyboardVerticalOffset={'60'}
      behavior={'padding'}>
      <ScrollView
        showsVerticalScrollIndicator={false}
        contentContainerStyle={styles.container}>
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
        <Header
          backicon={constants.images.backIcon}
          titleColor={constants.colors.greenText}
          title="Edit Profile"
          navigation={navigation}
        />

        <View style={styles.formView}>
          <View style={styles.formWrap}>
            <TouchableOpacity
              onPress={() => uploadImage()}
              style={{
                justifyContent: 'center',
                alignItems: 'center',
                backgroundColor: '#ffffff',
                margin: 10,
                borderRadius: 10,
              }}>
              {image ? (
                <Image
                  source={{ uri: localURL || image }}
                  style={styles.profileImage}
                />
              ) : (
                  <Image
                    source={
                      localURL
                        ? { uri: localURL }
                        : constants.images.defaultUserImage
                    }
                    style={styles.profileImage}
                  />
                )}
            </TouchableOpacity>

            <View style={styles.formField}>
              <Text style={styles.fieldName}>FIRST NAME</Text>
              <View style={styles.fieldInputWrap}>
                <TextInput
                  style={styles.fieldInput}
                  onChangeText={(text) => setFirstName(text)}
                  value={first_name}
                  autoCompleteType={'off'}
                  autoCorrect={false}
                />
                <View style={styles.editWrap}>
                  <Image
                    source={constants.images.ic_edit}
                    style={{ width: 13, height: 11.5 }}
                  />
                </View>
              </View>
            </View>

            <View style={styles.formField}>
              <Text style={styles.fieldName}>LAST NAME</Text>
              <View style={styles.fieldInputWrap}>
                <TextInput
                  style={styles.fieldInput}
                  onChangeText={(text) => setLastName(text)}
                  value={last_name}
                  autoCompleteType={'off'}
                  autoCorrect={false}
                />
                <View style={styles.editWrap}>
                  <Image
                    source={constants.images.ic_edit}
                    style={{ width: 13, height: 11.5 }}
                  />
                </View>
              </View>
            </View>

            <View style={styles.formField}>
              <Text style={styles.fieldName}>EMAIL</Text>
              <View style={styles.fieldInputWrap}>
                <TextInput
                  style={styles.fieldInput}
                  onChangeText={(text) => setEmail(text)}
                  value={email}
                  autoCompleteType={'off'}
                  autoCorrect={false}
                  autoCapitalize={'none'}
                  onBlur={() => {
                    if (email.length) setEmailError(validateEmail(email));
                  }}
                />
                <View style={styles.editWrap}>
                  <Image
                    source={constants.images.ic_edit}
                    style={{ width: 13, height: 11.5 }}
                  />
                </View>
              </View>
            </View>

            <View style={styles.formField}>
              <Text style={styles.fieldName}>LANGUAGE YOU SPEAK</Text>
              <View style={styles.fieldInputWrap}>
                <TextInput
                  style={styles.fieldInput}
                  onChangeText={(text) => setLanguage(text)}
                  value={language}
                  editable={false}
                />
                <TouchableOpacity
                  onPress={() => setShowLanguages(true)}
                  style={styles.editWrap}>
                  <Image
                    source={constants.images.ic_edit}
                    style={{ width: 13, height: 11.5 }}
                  />
                </TouchableOpacity>
              </View>
            </View>
            {role == 1 ? (
              <View style={styles.formField}>
                <Text style={styles.fieldName}>YOUR QUALIFICATION</Text>
                <View style={styles.fieldInputWrap}>
                  <TextInput
                    style={styles.fieldInput}
                    onChangeText={(text) => setQualification(text)}
                    value={qualification}
                    autoCompleteType={'off'}
                    autoCorrect={false}
                  />
                  <View style={styles.editWrap}>
                    <Image
                      source={constants.images.ic_edit}
                      style={{ width: 13, height: 11.5 }}
                    />
                  </View>
                </View>
              </View>
            ) : (
                <View />
              )}
            {role == 1 ? (
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
                  <View style={styles.editWrap}>
                    <Image
                      source={constants.images.ic_edit}
                      style={{ width: 13, height: 11.5 }}
                    />
                  </View>
                </View>
              </View>
            ) : (
                <View />
              )}
            {role == 1 ? (
              <View style={styles.formField}>
                <Text style={styles.fieldName}>SPECIALISM</Text>
                <View style={styles.fieldInputWrap}>
                  <TextInput
                    style={styles.fieldInput}
                    value={selectedSpeciality}
                    editable={false}
                    autoCompleteType={'off'}
                    autoCorrect={false}
                  />
                  <TouchableOpacity
                    onPress={() => setShowSpecialism(true)}
                    style={styles.editWrap}>
                    <Image
                      source={constants.images.ic_edit}
                      style={{ width: 13, height: 11.5 }}
                    />
                  </TouchableOpacity>
                </View>
              </View>
            ) : (
                <View />
              )}

            <SubmitButton
              title={'SAVE'}
              submitFunction={() => editProfileHandler()}
            />
          </View>
        </View>
        <View style={styles.emptyView} />
      </ScrollView>
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
    </KeyboardAwareScrollView>
  );
};

const mapStateToProps = (state) => ({
  userData: state.user.userData,
  userToken: state.user.userToken,
});

export default connect(mapStateToProps)(EditProfile);
