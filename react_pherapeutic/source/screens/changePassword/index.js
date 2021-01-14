import React, {useState} from 'react';
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
import SubmitButton from '../../components/submitButton';
import APICaller from '../../utils/APICaller';
import AsyncStorage from '@react-native-async-storage/async-storage';
import Events from '../../utils/events';
import {connect} from 'react-redux';
import {saveUser} from '../../redux/actions/user';
import AwesomeAlert from 'react-native-awesome-alerts';
import {validateEmail} from '../../utils/validateStrings';
import Header from '../../components/Header';

const {height, width} = Dimensions.get('window');

const ChangePassword = (props) => {
  const [old, setOld] = useState('');
  const [password, setNew] = useState('');
  const [confirm, setConfirm] = useState('');
  const [showAlert, setShowAlert] = useState(false);
  const [message, setMessage] = useState('');
  const [success, setSuccess] = useState(false);

  const {navigation, dispatch, userToken} = props;

  const changePasswordHandler = () => {
    if (!old) {
      setMessage('Please Enter Old Password.');
      setShowAlert(true);
    } else if (!password || !confirm) {
      setMessage('Please Enter New Password and Confirm.');
      setShowAlert(true);
    } else if (password !== confirm) {
      setMessage('Please Enter The Same Password.');
      setShowAlert(true);
    } else {
      const body = {
        old_password: old,
        password,
        confirm_password: confirm,
      };
      const endpoint = 'user/changePassword';
      const method = 'POST';
      const headers = {
        'Content-Type': 'application/json',
        Authorization: `Bearer ${userToken}`,
        Accept: 'application/json',
      };
      APICaller(endpoint, method, body, headers)
        .then((response) => {
          console.log('response changing password => ', response['data']);
          const {status, statusCode, message, data} = response['data'];
          if (status === 'success') {
            // dispatch(saveUserProfile(data));
            setMessage('Your Password has Changed Successfully.');
            setShowAlert(true);
            setSuccess(true);
          }
        })
        .catch((error) => {
          console.log('error changing password => ', error['data']);
          const {status, statusCode, message, data} = error['data'];
          setMessage(message);
          setShowAlert(true);
        });
    }
  };

  return (
    <View style={styles.container}>
      <Image
        source={constants.images.background}
        resizeMode={'stretch'}
        style={styles.containerBackground}
      />

      <Header title="Change Password" navigation={navigation} />

      <View style={styles.formView}>
        <View style={styles.formWrap}>
          <View style={styles.formField}>
            <Text style={styles.fieldName}>OLD PASSWORD</Text>
            <View style={styles.fieldInputWrap}>
              <TextInput
                style={styles.fieldInput}
                onChangeText={(text) => setOld(text)}
                value={old}
                autoCapitalize={'none'}
                secureTextEntry={true}
              />
            </View>
          </View>

          <View style={styles.formField}>
            <Text style={styles.fieldName}>NEW PASSWORD</Text>
            <View style={styles.fieldInputWrap}>
              <TextInput
                style={styles.fieldInput}
                onChangeText={(text) => setNew(text)}
                value={password}
                autoCapitalize={'none'}
                secureTextEntry={true}
              />
            </View>
          </View>

          <View style={styles.formField}>
            <Text style={styles.fieldName}>CONFIRM NEW PASSWORD</Text>
            <View style={styles.fieldInputWrap}>
              <TextInput
                style={styles.fieldInput}
                onChangeText={(text) => setConfirm(text)}
                value={confirm}
                autoCapitalize={'none'}
                secureTextEntry={true}
              />
            </View>
          </View>

          <SubmitButton
            title={'SUBMIT'}
            submitFunction={() => changePasswordHandler()}
          />
        </View>
      </View>
      <AwesomeAlert
        show={showAlert}
        showProgress={false}
        message={message}
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
          if (success) navigation.goBack();
        }}
      />
    </View>
  );
};

const mapStateToProps = (state) => ({
  userToken: state.user.userToken,
});

export default connect(mapStateToProps)(ChangePassword);
