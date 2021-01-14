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
import constants from '../../utils/constants';
import styles from './styles';
import SubmitButton from '../../components/submitButton';
import APICaller from '../../utils/APICaller';
import { KeyboardAwareScrollView } from 'react-native-keyboard-aware-scroll-view';
import Events from '../../utils/events';
import CodeInput from 'react-native-confirmation-code-input';
// import { Dropdown } from 'react-native-material-dropdown';
import AwesomeAlert from 'react-native-awesome-alerts';
import { saveUser } from '../../redux/actions/user';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { connect } from 'react-redux';

const { height, width } = Dimensions.get('window');

const VerifyOTP = (props) => {
  const [otp, setOTP] = useState('');
  const [showAlert, setShowAlert] = useState(false);
  const [alertMessage, setAlert] = useState('OTP is incorrect.');

  const { navigation, route, dispatch } = props;
  const { params } = route;

  const verifyOTPHandler = () => {
    if (otp.length == 6) {
      const body = {
        user_id: params['user_id'],
        otp,
      };
      APICaller('verifyOtp', 'POST', body)
        .then((response) => {
          Events.trigger('showModalLoader');
          console.log('response verifying otp => ', response);
          const { data, message, status, sttusCode } = response['data'];
          if (message == 'Otp verified successfully') {
            AsyncStorage.setItem('userData', JSON.stringify(data));
            dispatch(saveUser(data));
            Events.trigger('hideModalLoader');
            navigation.navigate('Onboarding');
          } else {
            console.log('wrong otp');
            setShowAlert(true);
          }
        })
        .catch((error) => {
          console.log('error verifying otp => ', error);
          Events.trigger('hideModalLoader');
          setShowAlert(true);
        });
    }
  };

  const resendOTPHandler = () => {
    Events.trigger('showModalLoader');
    const body = {
      user_id: params['user_id'],
      otp,
    };
    APICaller('resendOtp', 'POST', body)
      .then((response) => {
        console.log('response verifying otp => ', response);
        const { data, message, status, sttusCode } = response['data'];
        if (message == 'Otp resend successfully!') {
          Events.trigger('hideModalLoader');
          setAlert(message);
          setShowAlert(true);
        } else {
           Events.trigger('hideModalLoader');
           setAlert(message);
           setShowAlert(true);
        }
      })
      .catch((error) => {
         Events.trigger('hideModalLoader');
        setShowAlert(true);
      });
  };


  return (
    <KeyboardAwareScrollView
      showsVerticalScrollIndicator={false}
      contentContainerStyle={{ width: '100%', flex: 1 }}
      keyboardVerticalOffset={'60'}
      behavior={'padding'}>
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
          <TouchableOpacity
            onPress={() => navigation.goBack()}
            style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
            <Image
              source={constants.images.backIcon}
              style={{ height: 18, width: 10, margin: 10 }}
            />
          </TouchableOpacity>
          <View
            style={{ flex: 5, justifyContent: 'center', alignItems: 'center' }}>
            <Text style={styles.headingText}>Verify email</Text>
          </View>
          <View style={{ flex: 1 }} />
        </View>
        <View style={styles.content}>
          <View style={styles.heading}>
            <Text style={styles.topText}>
              Enter the six digit code we sent you
            </Text>
            <Text style={styles.topText}>via email to continue.</Text>
          </View>
          <View style={styles.formWrap}>
            <View style={styles.formField}>
              <CodeInput
                keyboardType="numeric"
                codeLength={6}
                className="border-circle"
                compareWithCode={null}
                autoFocus={true}
                onFulfill={(code) => setOTP(code)}
                codeInputStyle={styles.codeInput}
                containerStyle={styles.codeInputContainer}
              />
            </View>
            <View style={styles.formField}>
              <SubmitButton
                title={'Continue'}
                submitFunction={() => verifyOTPHandler()}
              />
            </View>
            <View style={[styles.formField, { height: height * 0.03 }]}>
              <Text>
                Didn't get the code?{' '}

              </Text>
              <TouchableOpacity onPress={() => resendOTPHandler()}>
                <Text  style={{ color: constants.colors.greenText }}>Resend</Text>
              </TouchableOpacity>
            </View>
          </View>
        </View>
      </View>
      <AwesomeAlert
        show={showAlert}
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

export default connect()(VerifyOTP);
