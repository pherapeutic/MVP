import React, { useEffect, useState } from 'react';
import {
  View,
  Text,
  TouchableOpacity,
  StyleSheet,
  Dimensions,
  Image,
  TextInput,
  Switch,
} from 'react-native';
import constants from '../../utils/constants';
import styles from './styles';
import LogoutAlert from '../../components/logoutAlert';
import { connect } from 'react-redux';
import Header from '../../components/Header';
import { ScrollView } from 'react-native-gesture-handler';
import APICaller from '../../utils/APICaller';
import { saveUserProfile } from '../../redux/actions/user';
import { AirbnbRating } from 'react-native-ratings';
const { height, width } = Dimensions.get('window');

const MyProfile = (props) => {
  const [showModal, setShowModal] = useState(false);
  const [switchValue, setSwitchValue] = useState(false);
  console.log('props', props);
  const { navigation, userToken, dispatch, userData } = props;
  const {
    email,
    first_name,
    image,
    is_email_verified,
    language_id,
    last_name,
    role,
    rating
  } = userData;
  useEffect(() => {
    getUserProfile();
  }, []);
  console.log('userToken', userToken);

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
        const { status, statusCode, message, data } = response['data'];
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
        resizeMode={'stretch'}
        source={constants.images.background}
        style={styles.containerBackground}
      />
      <Header title="My Profile" navigation={navigation} />

      <View style={styles.content}>
        <View style={styles.pofileView}>
          <View style={styles.profileWrap}>
            <View style={styles.profileImageWrap}>
              <Image
                style={styles.profileImageWrap}
                source={
                  image ? { uri: image } : constants.images.defaultUserImage
                }
              />
            </View>
            {role == '1' ? (
              <View style={styles.profileDetailView}>
                <Text
                  style={
                    styles.userNameText
                  }>{`${first_name} ${last_name}`}</Text>
                <TouchableOpacity
                  onPress={() => navigation.navigate('ViewProfile')}
                >
                  <Text style={[styles.buttonText, { color: constants.colors.lightBlue }]}>VIEW AND EDIT PROFILE</Text>
                </TouchableOpacity>
                <View style={{ height: 25, padding: 5, flexDirection: "row", borderColor: "rgb(24,173,189)", borderRadius: 10, backgroundColor: constants.colors.greenText }}>

                  <Text style={{ fontSize: 14, color: "white", textAlign: "left", paddingLeft: "2%", alignSelf: "center" }}> Rating: {parseInt(rating) } </Text>

                  <AirbnbRating
                    showRating={false}
                    isDisabled={true}
                    count={5}
                    size={10}
                    defaultRating={rating}
               //   defaultRating={1}
                    selectedColor="white"
                    ratingColor='#3498db'
                    ratingBackgroundColor="rgb(24,173,189)"
                    starContainerStyle="white"

                  />
                </View>

              </View>
            ) : (
                <View style={styles.profileDetailView}>
                  <Text
                    style={
                      styles.userNameText
                    }>{`${first_name} ${last_name}`}</Text>
                  <TouchableOpacity
                    onPress={() => navigation.navigate('ViewProfile')}
                    style={styles.editButton}>
                    <Text style={styles.buttonText}>VIEW AND EDIT PROFILE</Text>
                  </TouchableOpacity>
                </View>
              )}
          </View>
          {role == '1' ? (
            <View style={styles.probonoWrap}>
              <Text style={styles.itemNameText}>
                Are you open for doing pro bono work
              </Text>
              <Switch
                trackColor={'rgb(223,223,223)'}
                thumbColor={
                  switchValue
                    ? constants.colors.lightBlue
                    : constants.colors.white
                }
                onValueChange={(value) => setSwitchValue(value)}
                value={switchValue}
              />
            </View>
          ) : (
              <View />
            )}
        </View>
        <ScrollView>
          <View style={styles.optionsWrap}>
            {role == 0 && (
              <TouchableOpacity style={styles.optionItem}
                onPress={() => navigation.navigate('AppointmentHistory')}
              >
                <View style={styles.optionNameView}>
                  <Image
                    source={constants.images.history}
                    resizeMode={'contain'}
                    style={styles.optionImage}
                  />
                  <Text style={styles.itemNameText}>History</Text>
                </View>
                <View style={styles.nextArrowWrap}>
                  <Image source={constants.images.nextArrow} />
                </View>
              </TouchableOpacity>
            )}
            {role == 1 && (
              <TouchableOpacity style={styles.optionItem}
                onPress={() => navigation.navigate('PaymentHistory')}
              >
                <View style={styles.optionNameView}>
                  <Image
                    source={constants.images.history}
                    resizeMode={'contain'}
                    style={styles.optionImage}
                  />
                  <Text style={styles.itemNameText}>History</Text>
                </View>
                <View style={styles.nextArrowWrap}>
                  <Image source={constants.images.nextArrow} />
                </View>
              </TouchableOpacity>
            )}

            {role == 1 ? (
              <TouchableOpacity
                onPress={() => navigation.navigate('TherapistStatus')}
                style={styles.optionItem}>
                <View style={styles.optionNameView}>
                  <Image
                    source={constants.images.changePassword}
                    resizeMode={'contain'}
                    style={styles.optionImage}
                  />
                  <Text style={styles.itemNameText}>Change Online Status</Text>
                </View>
                <View style={styles.nextArrowWrap}>
                  <Image source={constants.images.nextArrow} />
                </View>
              </TouchableOpacity>
            ) : (
                <View />
              )}
            <TouchableOpacity
              onPress={() => navigation.navigate('ChangePassword')}
              style={styles.optionItem}>
              <View style={styles.optionNameView}>
                <Image
                  source={constants.images.changePassword}
                  resizeMode={'contain'}
                  style={styles.optionImage}
                />
                <Text style={styles.itemNameText}>Change Password</Text>
              </View>
              <View style={styles.nextArrowWrap}>
                <Image source={constants.images.nextArrow} />
              </View>
            </TouchableOpacity>
            {role == 1 && (
              <TouchableOpacity
                style={styles.optionItem}
                onPress={() => navigation.navigate('RatingList')}>
                <View style={styles.optionNameView}>
                  <Image
                    source={constants.images.aboutUs}
                    resizeMode={'contain'}
                    style={styles.optionImage}
                  />
                  <Text style={styles.itemNameText}>Rating & Reviews</Text>
                </View>
                <View style={styles.nextArrowWrap}>
                  <Image source={constants.images.nextArrow} />
                </View>
              </TouchableOpacity>
            )}
            {role == 0 && (
              <TouchableOpacity
                style={styles.optionItem}
                onPress={() => navigation.navigate('ManagePayment')}>
                <View style={styles.optionNameView}>
                  <Image
                    source={constants.images.aboutUs}
                    resizeMode={'contain'}
                    style={styles.optionImage}
                  />
                  <Text style={styles.itemNameText}>
                    Update payment information
                  </Text>
                </View>
                <View style={styles.nextArrowWrap}>
                  <Image source={constants.images.nextArrow} />
                </View>
              </TouchableOpacity>
            )}
            {/* <TouchableOpacity
              style={styles.optionItem}
              onPress={() => navigation.navigate('AboutUs')}>
              <View style={styles.optionNameView}>
                <Image
                  source={constants.images.aboutUs}
                  resizeMode={'contain'}
                  style={styles.optionImage}
                />
                <Text style={styles.itemNameText}>About Us</Text>
              </View>
              <View style={styles.nextArrowWrap}>
                <Image source={constants.images.nextArrow} />
              </View>
            </TouchableOpacity> */}

            <TouchableOpacity
              style={styles.optionItem}
              onPress={() => navigation.navigate('ContactUs')}>
              <View style={styles.optionNameView}>
                <Image
                  source={constants.images.contactUs}
                  resizeMode={'contain'}
                  style={styles.optionImage}
                />
                <Text style={styles.itemNameText}>Contact Us</Text>
              </View>
              <View style={styles.nextArrowWrap}>
                <Image source={constants.images.nextArrow} />
              </View>
            </TouchableOpacity>

            <TouchableOpacity
              style={styles.optionItem}
              onPress={() => navigation.navigate('PrivacyPolicy')}>
              <View style={styles.optionNameView}>
                <Image
                  source={constants.images.privacyPolicy}
                  resizeMode={'contain'}
                  style={styles.optionImage}
                />
                <Text style={styles.itemNameText}>Privacy Policy</Text>
              </View>
              <View style={styles.nextArrowWrap}>
                <Image source={constants.images.nextArrow} />
              </View>
            </TouchableOpacity>

            <TouchableOpacity
              style={styles.optionItem}
              onPress={() => navigation.navigate('TermsAndConditions')}>
              <View style={styles.optionNameView}>
                <Image
                  source={constants.images.termsAndConditions}
                  resizeMode={'contain'}
                  style={styles.optionImage}
                />
                <Text style={styles.itemNameText}>Terms and Conditions</Text>
              </View>
              <View style={styles.nextArrowWrap}>
                <Image source={constants.images.nextArrow} />
              </View>
            </TouchableOpacity>


            <TouchableOpacity
              onPress={() => navigation.navigate('faq')}
              style={styles.optionItem}>
              <View style={styles.optionNameView}>
                <Image
                  source={constants.images.faqs}
                  resizeMode={'contain'}

                  style={styles.optionImage}
                />
                <Text style={styles.itemNameText}>Help</Text>
              </View>
              <View style={styles.nextArrowWrap}>
                <Image source={constants.images.nextArrow} />
              </View>
            </TouchableOpacity>
            <TouchableOpacity
              onPress={() => {
                console.log('logout!!!');
                setShowModal((prevState) => !prevState);
              }}
              style={styles.optionItem}>
              <View style={styles.optionNameView}>
                <Image
                  source={constants.images.logout}
                  resizeMode={'contain'}
                  style={styles.optionImage}
                />
                <Text style={styles.itemNameText}>Logout</Text>
              </View>
              <View style={styles.nextArrowWrap}>
                <Image source={constants.images.nextArrow} />
              </View>
            </TouchableOpacity>
          </View>
        </ScrollView>
      </View>
      <LogoutAlert
        showModal={showModal}
        setShowModal={setShowModal}
        navigation={navigation}
      />
    </View>
  );
};

const mapStateToProps = (state) => ({
  userData: state.user.userData,
  userToken: state.user.userToken,
});

export default connect(mapStateToProps)(MyProfile);
