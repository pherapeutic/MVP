import React, {useEffect, useState} from 'react';
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
import {connect} from 'react-redux';
import Header from '../../components/Header';

const {height, width} = Dimensions.get('window');

const MyProfile = (props) => {
  const [showModal, setShowModal] = useState(false);
  const [switchValue, setSwitchValue] = useState(false);

  const {navigation, userData, dispatch} = props;
  const {
    email,
    first_name,
    image,
    is_email_verified,
    language_id,
    last_name,
    role,
  } = userData;
console.log(userData)
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
                source={image ? {uri: image} : constants.images.defaultUserImage}
              />
            </View>
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
                    : constants.colors.red
                }
                onValueChange={(value) => setSwitchValue(value)}
                value={switchValue}
              />
            </View>
          ) : (
            <View />
          )}
        </View>

        <View style={styles.optionsWrap}>
          <View style={styles.optionItem}>
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
          </View>
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

          <TouchableOpacity
            style={styles.optionItem}
            // onPress={() => navigation.navigate('ManagePayment')}
          >
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
          </TouchableOpacity>

          <View style={styles.optionItem}>
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
          </View>

          <View style={styles.optionItem}>
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
          </View>

          <View style={styles.optionItem}>
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
          </View>

          <View style={styles.optionItem}>
            <View style={styles.optionNameView}>
              <Image
                source={constants.images.faqs}
                resizeMode={'contain'}
                style={styles.optionImage}
              />
              <Text style={styles.itemNameText}>FAQs</Text>
            </View>
            <View style={styles.nextArrowWrap}>
              <Image source={constants.images.nextArrow} />
            </View>
          </View>

          <TouchableOpacity
            // onPress={() => navigation.navigate('MapScreen')}
            style={styles.optionItem}>
            <View style={styles.optionNameView}>
              <Image
                source={constants.images.faqs}
                resizeMode={'contain'}
                style={styles.optionImage}
              />
              <Text style={styles.itemNameText}>Location </Text>
            </View>
            <View style={styles.nextArrowWrap}>
              <Image source={constants.images.nextArrow} />
            </View>
          </TouchableOpacity>

          {/* 
          <TouchableOpacity
            // onPress={() => navigation.navigate('VideoCall')}
            style={styles.optionItem}>
            <View style={styles.optionNameView}>
              <Image
                source={constants.images.faqs}
                resizeMode={'contain'}
                style={styles.optionImage}
              />
              <Text style={styles.itemNameText}>Video </Text>
            </View>
            <View style={styles.nextArrowWrap}>
              <Image source={constants.images.nextArrow} />
            </View>
          </TouchableOpacity> */}

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
});

export default connect(mapStateToProps)(MyProfile);
