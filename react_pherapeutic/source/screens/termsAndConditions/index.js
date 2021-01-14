import React, {useState,useEffect} from 'react';
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

const TermsAndConditions = (props) => {
  const { userToken, userData, navigation } = props;
  const [data, setData] = useState('');
  useEffect(() => {
      getData();
  }, []);
  const getData = () => {
    Events.trigger('showModalLoader');
     
      APICaller('getTermsandConditions', 'GET', null, null)
          .then((response) => {
            Events.trigger('hideModalLoader');
              console.log("response is", response['data']);
              const {data, message, status, statusCode} = response['data'];
              setData(data[0].description)
          })
          .catch((error) => {
              console.log('error=> ', error);
          });

  }

  return (
    <View style={styles.container}>
      <Image
        source={constants.images.background}
        resizeMode={'stretch'}
        style={styles.containerBackground}
      />

      <Header title="Terms and Conditions" navigation={navigation} />

      <View style={styles.formView}>
        <View style={styles.formWrap}>
          <ScrollView
            style={{
              backgroundColor: constants.colors.white,
              paddingTop: height * 0.025,
              paddingBottom: height * 0.015,
              borderRadius: 10,
              marginRight: 10,
              marginLeft: 10,
              marginBottom: 5,
              width:'93%'
            }}
            showsVerticalScrollIndicator={false}>
            <Text
              style={{
                margin: 10,
                fontFamily: 'Poppins-Regular',
                color: 'grey',
              }}>
             {data}
            </Text>

         
          </ScrollView>
        </View>
      </View>
    </View>
  );
};

const mapStateToProps = (state) => ({

  userToken: state.user.userToken,
  userData: state.user.userData,
});
export default connect(mapStateToProps)(TermsAndConditions);

