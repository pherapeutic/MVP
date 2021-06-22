import React, {useEffect, useState} from 'react';
import {
  View,
  Text,
  TouchableOpacity,
  StyleSheet,
  Dimensions,
  Image,
  TextInput,
  ScrollView,
  Modal,
} from 'react-native';
import constants from '../../utils/constants';
import styles from './styles';
import LogoutAlert from '../../components/logoutAlert';
import {connect} from 'react-redux';
import APICaller from '../../utils/APICaller';
import {saveUserProfile} from '../../redux/actions/user';
import SubmitButton from '../../components/submitButton';
import AsyncStorage from '@react-native-async-storage/async-storage';
import ImagePicker from 'react-native-image-crop-picker';
import AwesomeAlert from 'react-native-awesome-alerts';
import {validateEmail} from '../../utils/validateStrings';
import {KeyboardAwareScrollView} from 'react-native-keyboard-aware-scroll-view';
import Events from '../../utils/events';
import Header from '../../components/Header';
import CheckBox from '@react-native-community/checkbox';
//import Geolocation from '@react-native-community/geolocation';

const {height, width} = Dimensions.get('window');
var language_select_var = [];
var specility_select_var = [];
var qualification_select_var = [];
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
  const [language_id, setLanguage_id] = useState([]);
  const [language_select, selectLanguage] = useState([]);
  const [modalVisible, setmodalVisible] = useState(true);
  const [success, setSuccess] = useState(false);
  const [qualifications, storeQualification] = useState([]);
  
  const [showLanguages, setShowLanguages] = useState(false);
  const [showQualification, setShowQaulification] = useState(false);
  const [showSpecialism, setShowSpecialism] = useState(false);
  const [selectedSpeciality, setSpeciality] = useState('');
  const [speciality_id, setSpeciality_id] = useState([]);
  const [speciality_select, selectSpeciality] = useState([]);
  const [years, setYears] = useState('');
  const [showAlert, setShowAlert] = useState(false);
  const [alertMessage, setAlert] = useState('Please Fill All Fields');
  const [readyToSubmit, setReadyToSibmit] = useState(true);
  const [qualification, setQualification] = useState([]);
  const [selectedqualification, setSelectqualification] = useState('');
  const [qualification_id, setQualification_id] = useState([]);
  const [imageURL, setImageURL] = useState(null);
  const [token, setToken] = useState(null);

  const editProfileHandler = () => {
    if (role == 1) {
      if (!first_name || !last_name || !years || !qualification) {
        setAlert('Fields can not be empty.');
        setShowAlert(true);
      } else {
        console.log(qualification_id);
        //   const body = {
        //   first_name:first_name,
        //   last_name:last_name,
        //   languages: [language_id],
        //   device_type: 1,
        //   fcm_token:token,
        //   experience:years,
        //   qualification:qualification,
        //   image: {
        //     uri: localURL, //Your Image File Path
        //     type: 'image/jpeg',
        //     // name: imageFile.filename,
        //     name: Math.random().toString(36).substring(7),
        //   },
        //   specialism:[speciality_id]
        // };
        // updateProfile(body);
        const formData = new FormData();
        formData.append('first_name', first_name);
        formData.append('last_name', last_name);
        for (var i = 0; i < language_id.length; i++) {
          let value = language_id[i];
          formData.append('languages[' + i + ']', value);
        }
        formData.append('device_type', 1);
        formData.append('fcm_token', token);
        formData.append('experience', years);
       // formData.append('qualification', qualification);
      
        for (var k1 = 0; k1 < qualification_id.length; k1++) {
          let value223 = qualification_id[k1];
          console.log(value223)
          formData.append('qualification[' + k1 + ']', value223);
        }
        //  formData.append('specialism', JSON.stringify(datass));
        for (var k = 0; k < speciality_id.length; k++) {
          let value22 = speciality_id[k];
          formData.append('specialism[' + k + ']', value22);
        }

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
        console.log("formData",formData);
        updateProfile(formData);
      }
    } else {
      if (!first_name || !last_name) {
        setAlert('Fields can not be empty.');
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
        for (var i = 0; i < language_id.length; i++) {
          let value = language_id[i];
          formData.append('languages[' + i + ']', value);
        }
        formData.append('device_type', 1);
        formData.append('fcm_token', token);
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
        console.log(
          'response updating user profile => ',
          response['data'].data,
        );
        const {status, statusCode, message, data} = response['data'];
        if (status === 'success') {
          dispatch(saveUserProfile(data));
          setAlert('Profile Update Successfully');
          setSuccess(true);
          setShowAlert(true);
        }
      })
      .catch((error) => {
        // alert(JSON.stringify(error));

        Events.trigger('hideModalLoader');
        console.log('error updating user profile => ', error['data']);
        const {status, statusCode, message, data} = error['data'];
        setAlert(message);
        setSuccess(true);
        setShowAlert(true);
      });
  };
  const {navigation, userData, dispatch, userToken} = props;

  const {image, is_email_verified, role} = userData;

  useEffect(() => {
    language_select_var = [];
    specility_select_var = [];
    qualification_select_var=[]
    setUserInfo(userData);
    getQualifications();
    getLanguages();
    getSpecialities();
   
    console.log('user data get => ', userData);
    setFirstName(userData['first_name']);
    setLastName(userData['last_name']);
    setEmail(userData['email']);
    // setLanguage(
    //   userData.languages.length ? userData['languages'][0]['title'] : 'English',
    // );
    // setLanguage_id(
    //   userData.languages.length ? userData['languages'][0]['id'] : '1',
    // );
    // setLanguage_id([...language_id,id]);
    if (userData.languages.length > 0) {
      const commaSepLang = userData.languages
        .map((item) => item.title)
        .join(', ');

      setLanguage(commaSepLang);

      let arr = [];
      let arrselectname = [];
      for (var i = 0; i < userData.languages.length; i++) {
        console.log(userData['languages'][i]['id']);
        //   console.log(userData['languages'][i]['id'])
        arr.push(userData['languages'][i]['id']);
        arrselectname.push(userData['languages'][i]['title']);
        //  setLanguage_id([...language_id, userData['languages'][i]['id']]);
      }
      language_select_var = arrselectname;
      //selectLanguage(arrselectname);
      setLanguage_id(arr);
    }
    setImageURL(userData['image']);

    if (role == 1) {
  //    setQualification(userData['qualification']);
      setYears(userData['experience'] ? userData['experience'].toString() : '');
      //setSpeciality(userData.specialism.length && userData['specialism'][0]['title']);
      // setSpeciality_id(userData.specialism.length && userData['specialism'][0]['id']);
      if (userData.specialism.length > 0) {
        const commaSepLism = userData.specialism
          .map((item) => item.title)
          .join(', ');

        setSpeciality(commaSepLism);
        let arrspl = [];
        let arrselectnamespl = [];
        for (var l = 0; l < userData.specialism.length; l++) {
          arrspl.push(userData['specialism'][l]['id']);
          arrselectnamespl.push(userData['specialism'][l]['title']);
          //  setLanguage_id([...language_id, userData['languages'][i]['id']]);
        }
        specility_select_var = arrselectnamespl;
        selectSpeciality(arrselectnamespl);
        setSpeciality_id(arrspl);
      }
      if (userData.qualification.length > 0) {
        const commaSepLismq = userData.qualification
          .map((item) => item.title)
          .join(', ');

        setQualification(commaSepLismq);
        let arrspl2 = [];
        let arrselectnamespl2 = [];
        for (var k = 0; k < userData.qualification.length; k++) {
          arrspl2.push(userData['qualification'][k]['id']);
          arrselectnamespl2.push(userData['qualification'][k]['title']);
          //  setLanguage_id([...language_id, userData['languages'][i]['id']]);
        }
        qualification_select_var = arrselectnamespl2;
     //   selectSpeciality(arrselectnamespl);
        setQualification_id(arrspl2);
      }
    }
    AsyncStorage.getItem('fcmToken').then((token) => {
      setToken(token);
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
  const addLanguageClick = (id, title) => {
    if (language_select_var.find((data) => data == title)) {
      let filteredArray2 = language_select_var.filter((item) => item !== title);
      //  selectLanguage(filteredArray2);
      language_select_var = filteredArray2;
      //  const commaSepLang2 = language_select.map(item => item).join(', ');
      const commaSepLang2 = filteredArray2.map((item) => item).join(', ');
      setLanguage(commaSepLang2);

      // setLanguage(commaSepLang2);
    } else {
      // if(language_select.length>0)
      // {
      //     language_select_var.push(title)
      //   selectLanguage([...language_select, title]);

      //  const commaSepLang3 = language_select.map(item => item).join(', ');
      //   setLanguage(commaSepLang3);
      language_select_var.push(title);
      const commaSepLang3 = language_select_var.map((item) => item).join(', ');
      setLanguage(commaSepLang3);
      // }else{
      //   setLanguage(title);
      // }
    }
    if (language_id.find((data) => data == id)) {
      let filteredArray = language_id.filter((item) => item !== id);
      setLanguage_id(filteredArray);
    } else {
      setLanguage_id([...language_id, id]);
    }
  };
  // setSpeciality_id(speciality['id']);
  // setSpeciality(speciality['title']);
  // setShowSpecialism(false);
  const addSpecilismClick = (id, title) => {
    console.log('id',id)
    console.log('title',title)
    if (specility_select_var.find((data) => data == title)) {
      let filteredArray2 = specility_select_var.filter(
        (item) => item !== title,
      );
      specility_select_var = filteredArray2;
      console.log('filteredArray2',filteredArray2)
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

  const addQualificationClick = (id, title) => {
    console.log('id',id)
    console.log('title',title)
    if (qualification_select_var.find((data) => data == title)) {
      let filteredArray21 = qualification_select_var.filter(
        (item) => item !== title,
      );
   
      qualification_select_var = filteredArray21;
      console.log('filteredArray2',filteredArray21)
      const commaSepLang2 = filteredArray21.map((item) => item).join(', ');
      setQualification(commaSepLang2);
    } else {
      qualification_select_var.push(title);
      const commaSepLang31 = qualification_select_var.map((item) => item).join(', ');
      console.log('commaSepLang3',commaSepLang31)
      setQualification(commaSepLang31);
    }
    if (qualification_id.find((data) => data == id)) {
      let filteredArray = qualification_id.filter((item) => item !== id);
      setQualification_id(filteredArray);
    } else {
      setQualification_id([...qualification_id, id]);
    }
  };
  // const ListView = () => (
  //   <ScrollView style={{ width: '100%' }}>

  //     <View style={{ justifyContent: 'center', alignItems: 'center', width: '100%' }}>
  //       <Image
  //         source={constants.images.background}
  //         resizeMode={'stretch'}
  //         style={styles.containerBackground}
  //       />
  //       {languages.map((lang) => {
  //         return (
  //           <TouchableOpacity
  //             onPress={() => {
  //               //  setLanguage_id([lang['id']]);
  //               setLanguage_id([...language_id, lang['id']]);
  //               setLanguage(lang['title']);
  //               setShowLanguages(false);
  //             }}
  //             style={{
  //               height: Dimensions.get('window').height * 0.05,
  //               width: Dimensions.get('window').width * 0.6,
  //               paddingHorizontal: 2,
  //               justifyContent: 'center',
  //               alignItems: 'flex-start',
  //             }}>
  //             <Text>{lang['title']}</Text>
  //           </TouchableOpacity>
  //         );
  //       })}
  //     </View>
  //   </ScrollView>

  // );

  const QualificationList = () => (
    <View style={{justifyContent: 'center', alignItems: 'center'}}>
      {qualifications.map((quali) => {
        return (
          <TouchableOpacity
            onPress={() => {
              setQualification_id(quali['title']);
              setQualification(quali['title']);
              setShowQaulification(false);
            }}
            style={{
              height: Dimensions.get('window').height * 0.05,
              width: Dimensions.get('window').width * 0.6,
              paddingHorizontal: 2,
              justifyContent: 'center',
              alignItems: 'flex-start',
            }}>
            <Text>{quali['title']}</Text>
          </TouchableOpacity>
        );
      })}
    </View>
  );

  const SpecialitiesList = () => (
    <View style={{justifyContent: 'center', alignItems: 'center'}}>
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
      contentContainerStyle={{width: '100%', flex: 1}}
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
                  source={{uri: localURL || image}}
                  style={styles.profileImage}
                />
              ) : (
                <Image
                  source={
                    localURL
                      ? {uri: localURL}
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
                  onChangeText={(text) => setFirstName(text.replace(/[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, ''))}
                  value={first_name}
                  autoCompleteType={'off'}
                  autoCorrect={false}
                />
                <View style={styles.editWrap}>
                  <Image
                    source={constants.images.ic_edit}
                    style={{width: 13, height: 11.5}}
                  />
                </View>
              </View>
            </View>

            <View style={styles.formField}>
              <Text style={styles.fieldName}>LAST NAME</Text>
              <View style={styles.fieldInputWrap}>
                <TextInput
                  style={styles.fieldInput}
                  onChangeText={(text) => setLastName(text.replace(/[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, ''))}
                  value={last_name}
                  autoCompleteType={'off'}
                  autoCorrect={false}
                />
                <View style={styles.editWrap}>
                  <Image
                    source={constants.images.ic_edit}
                    style={{width: 13, height: 11.5}}
                  />
                </View>
              </View>
            </View>
            {email != null && (
              <View style={styles.formField}>
                <Text style={styles.fieldName}>EMAIL</Text>
                <View style={styles.fieldInputWrap}>
                  <TextInput
                    style={styles.fieldInput}
                    onChangeText={(text) => setEmail(text)}
                    value={email}
                    editable={false}
                    autoCompleteType={'off'}
                    autoCorrect={false}
                    editable={false}
                    autoCapitalize={'none'}
                    onBlur={() => {
                      if (email.length) setEmailError(validateEmail(email));
                    }}
                  />
                  <View style={styles.editWrap}>
                    <Image
                      source={constants.images.ic_edit}
                      style={{width: 13, height: 11.5}}
                    />
                  </View>
                </View>
              </View>
            )}
            <View style={styles.formField}>
              <Text style={styles.fieldName}>LANGUAGE YOU SPEAK </Text>
              <ScrollView>
                <View
                  style={{
                    //  height: 40,
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
                    {language}
                  </Text>
                  <TouchableOpacity
                    onPress={() => setShowLanguages(true)}
                    style={styles.editWrap}>
                    <Image
                      source={constants.images.ic_edit}
                      style={{width: 13, height: 11.5}}
                    />
                  </TouchableOpacity>
                </View>
              </ScrollView>
            </View>
            {role == 1 ? (
              <View style={styles.formField}>
                <Text style={styles.fieldName}>YOUR QUALIFICATION</Text>
                {/* <View style={styles.fieldInputWrap}>
                  <TextInput
                    style={styles.fieldInput}
                    onChangeText={(text) => setQualification(text)}
                    value={qualification}
                    autoCompleteType={'off'}
                    autoCorrect={false}
                    editable={false}
                  />
                  <TouchableOpacity
                    style={styles.editWrap}
                    onPress={() => setShowQaulification(true)}>
                    <Image
                      source={constants.images.ic_edit}
                      style={{width: 13, height: 11.5}}
                    />
                  </TouchableOpacity>
                </View> */}
                 <ScrollView>
                  <View
                    style={{
                      //height: 40,
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
                      {qualification}
                    </Text>
                    <TouchableOpacity
                      onPress={() => setShowQaulification(true)}
                      style={styles.editWrap}>
                      <Image
                        source={constants.images.ic_edit}
                        style={{width: 13, height: 11.5}}
                      />
                    </TouchableOpacity>
                  </View>
                </ScrollView>
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
                    onChangeText={(text) => setYears(text.replace(/[^0-9]/g, ''))}
                    value={years}
                    autoCompleteType={'off'}
                    autoCorrect={false}
                   // keyboardType={'number-pad'}
                    numeric
                    keyboardType="number-pad"
                    maxLength={2}
                  /> 
                  <View style={styles.editWrap}>
                    <Image
                      source={constants.images.ic_edit}
                      style={{width: 13, height: 11.5}}
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
                <ScrollView>
                  <View
                    style={{
                      //height: 40,
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
                    <TouchableOpacity
                      onPress={() => setShowSpecialism(true)}
                      style={styles.editWrap}>
                      <Image
                        source={constants.images.ic_edit}
                        style={{width: 13, height: 11.5}}
                      />
                    </TouchableOpacity>
                  </View>
                </ScrollView>
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
        show={showQualification}
        closeOnTouchOutside={true}
        onConfirmPressed={() => {
          setShowQaulification(false);
        }}
        onDismiss={() => {
          setShowQaulification(false);
        }}
        customView={<QualificationList />}
      /> */}
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
                        addQualificationClick(quali['id'],quali['title']);
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
          if (success) navigation.goBack();
        }}
        onConfirmPressed={() => {
          setShowAlert(false);
          if (success) navigation.goBack();
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
