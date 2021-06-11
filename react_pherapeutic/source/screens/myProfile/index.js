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
import Scale from '../../utils/Scale';
import StarRating from 'react-native-star-rating';
const { height, width } = Dimensions.get('window');

const MyProfile = (props) => {
  const [showModal, setShowModal] = useState(false);
  const [switchValue, setSwitchValue] = useState(false);
  const [client_id, setclient_id] = useState('');
  const [stripe_connect_url, setStripe_connect_url] = useState('');

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
    rating,
    login_type,
  } = userData;
  useEffect(() => {
    getUserProfile();
    getstripeData();
    // console.log(this.state.client_id)
  }, []);
  console.log('userToken', userToken);
  const getstripeData = () => {
    const endpoint = 'stripeData';
    const method = 'GET';
    const headers = {
      'Content-Type': 'application/json',
      Authorization: `Bearer ${userToken}`,
      Accept: 'application/json',
    };
    //consoler.log(headers)
    APICaller(endpoint, method, null, headers)
      .then((response) => {
        //  console.log('response getting user profile => ', response['data']);
        const { status, statusCode, message, data } = response['data'];
        if (status === 'success') {
          // console.log(data.client_id)
          //  this.setState('client_id',data.client_id)
          setclient_id(data.client_id);
          setStripe_connect_url(data.stripe_connect_url);
        }
      })
      .catch((error) => {
        //  console.log('error getting user profile => ', error);
      });
  };
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
          setSwitchValue(data.pro_bono_work)
        }
      })
      .catch((error) => {
        console.log('error getting user profile => ', error);
      });
  };
  update_bono = (value) => {
    console.log(value)
    setSwitchValue(value)
    const endpoint = 'bonoWorkStatus';
    const method = 'POST';
    const headers = {
      'Content-Type': 'application/json',
      Authorization: `Bearer ${userToken}`,
      Accept: 'application/json',
    };
    const formData = new FormData();
    formData.append('bono_work', value==true?1:0);
    console.log(formData)
    APICaller(endpoint, method, formData, headers)
      .then((response) => {
        console.log('=====> ', response['data']);
        const { status, statusCode, message, data } = response['data'];
        if (status === 'success') {

          // dispatch(saveUserProfile(data));
          // // setAlert('Profile Update Successfully')
          // setShowAlert(true);
          // setlastModalVisible(0)
        }
      })
      .catch((error) => {
        const { data } = error;
        if (data.statusCode === 401) {

        }
        console.log('error changing online status => ', error['data']);
      });
  }
  return (
    <View style={styles.container}>
      <Image
        resizeMode={'stretch'}
        source={constants.images.background}
        style={styles.containerBackground}
      />
      <Header title="My Profile" navigation={navigation} />

      <View style={styles.content}>
        {role == 0 ? (
          <View
            style={[
              styles.pofileView,
              {
                flex: 0.7,

                padding: 5,
              },
            ]}>
            <View
              style={{
                flexDirection: 'row',
              }}>
              <View style={styles.profileImageWrap}>
                <Image
                  style={styles.profileImageWrap}
                  source={
                    image ? { uri: image } : constants.images.defaultUserImage
                  }
                />
              </View>

              <View
                style={[
                  styles.profileDetailView,
                  {
                    height:
                      Platform.OS == 'android' ? height * 0.1 : height / 9,
                  },
                ]}>
                <Text
                  style={
                    styles.userNameText
                  }>{`${first_name} ${last_name}`}</Text>
                <TouchableOpacity
                  onPress={() => navigation.navigate('ViewProfile')}
                  style={styles.editButton}>
                  <Text
                    style={[
                      styles.buttonText,
                      {
                        textAlign: 'center',
                        fontWeight: 'bold',
                      },
                    ]}>
                    VIEW AND EDIT PROFILE
                  </Text>
                </TouchableOpacity>
              </View>
            </View>
          </View>
        ) : null}
        {/* <View
          style={[
            styles.pofileView,
            {
              flex: role == '1' ? 4 : 0.7,

              padding: 5,
            },
          ]}>
          <View style={styles.profileWrap}>
            <View style={styles.profileImageWrap}>
              <Image
                style={styles.profileImageWrap}
                source={
                  image ? {uri: image} : constants.images.defaultUserImage
                }
              />
            </View>
            {role == '1' ? (
              <View
                style={[
                  styles.profileDetailView,
                  {
                    // height:
                    //   Platform.OS == 'android' ? height * 0.09 : height / 7.5,
                    //flex: 1,
                    height: height / 8,
                  },
                ]}>
                <Text
                  style={
                    styles.userNameText
                  }>{`${first_name} ${last_name}`}</Text>

                <TouchableOpacity
                  onPress={() => navigation.navigate('ViewProfile')}>
                  <Text
                    style={[
                      styles.buttonText,
                      {color: constants.colors.lightBlue, textAlign: 'center'},
                    ]}>
                    VIEW AND EDIT PROFILE
                  </Text>
                </TouchableOpacity>
                <View
                  style={{
                    height: 30,
                    //padding: 5,
                    flexDirection: 'row',
                    borderColor: 'rgb(24,173,189)',
                    borderRadius: 10,

                    backgroundColor: constants.colors.greenText,
                  }}>
                  <Text
                    style={{
                      fontSize: 14,
                      color: 'white',
                      textAlign: 'left',
                      paddingLeft: '2%',
                      alignSelf: 'center',
                    }}>
                    {' '}
                    Rating: {parseInt(rating) || 0}{' '}
                  </Text>

                  <AirbnbRating
                    showRating={false}
                    isDisabled={true}
                    //count={5}

                    count={5}
                    size={25}
                    defaultRating={parseInt(rating) || rating}
                    //   defaultRating={1}
                    selectedColor="white"
                    ratingColor="#3498db"
                    ratingBackgroundColor="rgb(24,173,189)"
                    starContainerStyle="white"
                  />
                </View>
              </View>
            ) : (
              <View
                style={[
                  styles.profileDetailView,
                  {
                    height:
                      Platform.OS == 'android' ? height * 0.09 : height / 9,
                  },
                ]}>
                <Text
                  style={
                    styles.userNameText
                  }>{`${first_name} ${last_name}`}</Text>
                <TouchableOpacity
                  onPress={() => navigation.navigate('ViewProfile')}
                  style={styles.editButton}>
                  <Text
                    style={[
                      styles.buttonText,
                      {
                        textAlign: 'center',
                      },
                    ]}>
                    VIEW AND EDIT PROFILE
                  </Text>
                </TouchableOpacity>
              </View>
            )}
          </View>
          {role == '1' ? (
            <View
              style={{
                flexDirection: 'row',
                alignItems: 'center',
                justifyContent: 'space-evenly',
                margin: 10,
              }}>
              <Text
                numberOfLines={2}
                style={[
                  styles.itemNameText,
                  {
                    flex: 1,
                  },
                ]}>
                Are you open for doing pro bono work
              </Text>
              <Switch
                trackColor={{true: 'rgb(223,223,223)'}}
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
        </View> */}
        <ScrollView>
          <View style={styles.optionsWrap}>
            {role == 1 ? (
              <View
                style={{
                  height: height / 4,
                  padding: 10,
                  borderRadius: 10,
                  width: width * 0.9,
                  flexDirection: 'column',
                  alignItems: 'center',
                  justifyContent: 'center',
                  borderBottomColor: 'rgb(245,245,245)',
                  borderBottomWidth: 0.5,
                  backgroundColor: 'white',
                }}>
                <View
                  style={{
                    flexDirection: 'row',
                    width: '100%',
                    height: '70%',

                    justifyContent: 'space-evenly',
                    alignItems: 'center',
                  }}>
                  <View style={styles.profileImageWrap}>
                    <Image
                      style={styles.profileImageWrap}
                      source={
                        image ? { uri: image } : constants.images.defaultUserImage
                      }
                    />
                  </View>
                  <View
                    style={{
                      flexDirection: 'column',
                      alignItems: 'flex-start',
                      justifyContent: 'space-evenly',
                      height: '89%',
                      flex: 1,

                      marginLeft: 10,
                    }}>
                    <Text
                      style={
                        styles.userNameText
                      }>{`${first_name} ${last_name}`}</Text>

                    <TouchableOpacity
                      onPress={() => navigation.navigate('ViewProfile')}>
                      <Text
                        style={[
                          styles.buttonText,
                          {
                            color: constants.colors.lightBlue,
                            textAlign: 'center',
                            fontWeight: 'bold',
                          },
                        ]}>
                        View and edit profile
                      </Text>
                    </TouchableOpacity>
                    <View
                      style={{
                        height: 25,
                        paddingLeft: 10,
                        paddingRight: 10,
                        //padding: 5,
                        paddingLeft: 10, paddingRight: 10,
                        flexDirection: 'row',
                        borderColor: 'rgb(24,173,189)',
                        borderRadius: 10,

                        backgroundColor: constants.colors.greenText,
                      }}>
                      <Text
                        style={{
                          fontSize: 14,
                          color: 'white',
                          textAlign: 'left',
                          paddingLeft: '2%',
                          alignSelf: 'center',
                        }}>
                        {' '}
                        Rating: {parseInt(rating) || 0}{' '}
                      </Text>

                      {/* <AirbnbRating
                        showRating={false}
                        isDisabled={true}
                        //count={5}

                        count={5}
                        size={15}
                        defaultRating={parseInt(rating) || 0}
                        //   defaultRating={1}
                        selectedColor="white"
                        ratingColor="#3498db"
                        ratingBackgroundColor="rgb(24,173,189)"
                        starContainerStyle="white"
                      /> */}
                      <StarRating
                        starSize={15}
                        disabled={true}
                        maxStars={5}
                        rating={parseInt(rating) || 0}
                        starStyle={{ color: 'white', marginTop: 4 }}

                      //selectedStar={(rating) => this.onStarRatingPress(rating)}
                      />
                    </View>
                  </View>
                </View>
                <View
                  style={{
                    flexDirection: 'row',
                    alignItems: 'center',
                    justifyContent: 'space-around',

                    width: '100%',
                    margin: 10,
                  }}>
                  <Text
                    numberOfLines={2}
                    style={[
                      styles.itemNameText,
                      {
                        flex: 1,
                      },
                    ]}>
                    Are you open for doing pro bono work
                  </Text>
                  <Switch
                    trackColor={{ true: 'rgb(223,223,223)' }}
                    thumbColor={
                      switchValue==1
                        ? constants.colors.lightBlue
                        : constants.colors.white
                    }
                    onValueChange={(value) => update_bono(value)}
                    value={switchValue==1?true:false}
                  />
                </View>
              </View>
            ) : null}
            {role == 0 && (
              <TouchableOpacity
                style={styles.optionItem}
                onPress={() => navigation.navigate('AppointmentHistory')}>
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
              <TouchableOpacity
                style={styles.optionItem}
                onPress={() => navigation.navigate('PaymentHistory')}>
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
              <TouchableOpacity
                style={styles.optionItem}
                onPress={() =>
                  navigation.navigate('StripeConnect', {
                    client_id: client_id,
                    stripe_connect_url: stripe_connect_url,
                  })
                }>
                <View style={styles.optionNameView}>
                  <Image
                    source={constants.images.credit_card}
                    resizeMode={'contain'}
                    style={styles.optionImage}
                  />
                  <Text style={styles.itemNameText}>Payment Settings</Text>
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
            {login_type == 0 && (
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
            )}
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
