import React, {useEffect, useState} from 'react';
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
import LogoutAlert from '../../components/logoutAlert';
import {connect} from 'react-redux';
import APICaller from '../../utils/APICaller';
import {saveUserProfile} from '../../redux/actions/user';
import SubmitButton from '../../components/submitButton';
import LinearGradient from 'react-native-linear-gradient';
import Header from '../../components/Header';

const {height, width} = Dimensions.get('window');

const ViewProfile = (props) => {
  const [showModal, setShowModal] = useState(false);
  const [language, setLanguage] = useState('English');

  const {navigation, userData, userToken, dispatch, userProfile} = props;
  const {
    address,
    email,
    experience,
    first_name,
    image,
    is_email_verified,
    languages,
    last_name,
    latitude,
    longitude,
    notification_status,
    online_status,
    qualification,
    role,
    specialism,
    user_id,
  } = userData;

  useEffect(() => {
    getUserProfile();
  }, []);

  const getUserProfile = () => {
    const endpoint = 'user/profile';
    const method = 'GET';
    const headers = {
      'Content-Type': 'application/json',
      Authorization: `Bearer ${userToken}`,
      Accept: 'application/json',
    };
    APICaller(endpoint, method, null, headers)
      .then((response) => {
        console.log('response getting user profile => ', response['data']);
        const {status, statusCode, message, data} = response['data'];
        if (status === 'success') {
          dispatch(saveUserProfile(data));
        }
      })
      .catch((error) => {
        console.log('error getting user profile => ', error);
      });
  };

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
      <Header
        title="View Profile"
        navigation={navigation}
        titleColor={constants.colors.greenText}
        backicon={constants.images.backIcon}
        titleflex={0.7}
        RightComponent={() => (
          <TouchableOpacity
            onPress={() => navigation.navigate('EditProfile')}
            style={{justifyContent: 'center', alignItems: 'center'}}>
            <LinearGradient
              start={{x: 0, y: 1}}
              end={{x: 1, y: 1}}
              colors={['#228994', '#3BD8E5']}
              style={{
                justifyContent: 'center',
                alignItems: 'center',
                height: Dimensions.get('window').height * 0.032,
                width: Dimensions.get('window').width * 0.15,
                borderRadius: 3,
              }}>
              <Text
                style={{
                  color: constants.colors.white,
                  fontWeight: '500',
                  fontSize: 13,
                }}>
                Edit
              </Text>
            </LinearGradient>
          </TouchableOpacity>
        )}
      />

      <View style={styles.formView}>
        <View style={styles.formWrap}>
          <View
            style={{
              justifyContent: 'center',
              alignItems: 'center',
              backgroundColor: '#ffffff',
              margin: 10,
              borderRadius: 10,
            }}>
            <Image
              source={image ? {uri: image} : constants.images.defaultUserImage}
              style={styles.profileImage}
            />
          </View>

          <View style={styles.formField}>
            <Text style={styles.fieldName}>NAME</Text>
            <View style={styles.fieldInputWrap}>
              <TextInput
                style={styles.fieldInput}
                onChangeText={(text) => setFirstName(text)}
                value={`${first_name} ${last_name}`}
                autoCompleteType={'off'}
                autoCorrect={'off'}
                editable={false}
              />
            </View>
          </View>

          <View style={styles.formField}>
            <Text style={styles.fieldName}>EMAIL</Text>
            <View style={styles.fieldInputWrap}>
              <TextInput
                style={styles.fieldInput}
                value={email}
                editable={false}
              />
            </View>
          </View>

          <View style={styles.formField}>
            <Text style={styles.fieldName}>LANGUAGE YOU SPEAK</Text>
            <View style={styles.fieldInputWrap}>
              <TextInput
                style={styles.fieldInput}
                value={languages.length ? languages[0]['title'] : 'English'}
                editable={false}
              />
            </View>
          </View>
          {role == 1 ? (
            <View style={{justifyContent: 'center', alignItems: 'center'}}>
              <View style={styles.formField}>
                <Text style={styles.fieldName}>YOUR QUALIFICATION</Text>
                <View style={styles.fieldInputWrap}>
                  <TextInput
                    style={styles.fieldInput}
                    value={qualification}
                    editable={false}
                  />
                </View>
              </View>

              <View style={styles.formField}>
                <Text style={styles.fieldName}>YEARS OF EXPERIENCE</Text>
                <View style={styles.fieldInputWrap}>
                  <TextInput
                    style={styles.fieldInput}
                    // value={experience.toString()}
                    value={experience}
                    editable={false}
                  />
                </View>
              </View>

              <View style={styles.formField}>
                <Text style={styles.fieldName}>SPECIALISM</Text>
                <View style={styles.fieldInputWrap}>
                  <TextInput
                    style={styles.fieldInput}
                    value={specialism[0]['title']}
                    editable={false}
                  />
                </View>
              </View>
            </View>
          ) : (
            <View />
          )}
        </View>
      </View>
    </View>
  );
};

const mapStateToProps = (state) => ({
  userData: state.user.userData,
  userToken: state.user.userToken,
});

export default connect(mapStateToProps)(ViewProfile);
