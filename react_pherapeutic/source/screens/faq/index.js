import React, { useEffect, useState } from 'react';
import {
  View,
  Text,
  TouchableOpacity,
  StyleSheet,
  Dimensions,
  Image,
  TextInput,
} from 'react-native';
import Modal from 'react-native-modal';
import constants from '../../utils/constants';
import styles from './styles';
import SubmitButton from '../../components/submitButton';
import APICaller from '../../utils/APICaller';
import AsyncStorage from '@react-native-async-storage/async-storage';
import Events from '../../utils/events';
import { connect } from 'react-redux';
import { saveUser } from '../../redux/actions/user';
import AwesomeAlert from 'react-native-awesome-alerts';
import { validateEmail } from '../../utils/validateStrings';
import Header from '../../components/Header';
import Accordion from 'react-native-collapsible-accordion';
import { ScrollView } from 'react-native-gesture-handler';
const { height, width } = Dimensions.get('window');

const faq = (props) => {
  const [old, setOld] = useState('');
  const [password, setNew] = useState('');
  const [confirm, setConfirm] = useState('');
  const [showAlert, setShowAlert] = useState(false);
  const [message, setMessage] = useState('');
  const [success, setSuccess] = useState(false);
  const [showMoreInfo, setShowMoreInfo] = useState(false)
  const [data, setData] = useState([]);
  const { navigation, dispatch, userToken } = props;
  useEffect(() => {
    getList();
  }, []);
  const getList = () => {
    let headers = {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${userToken}`,
      Accept: 'application/json',
    };
    APICaller('faq', 'GET', null, headers)
      .then((response) => {
        console.log("response is", response['data']);
        setData(response['data']['data'])
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
      <Header title="Frequently asked questions" navigation={navigation} />

      <View style={styles.content}>
        <ScrollView >
          {data.map((dataarr, index) => {
            return (
              <View style={styles.formWrap}>
                <Accordion
                  onChangeVisibility={(value) => {
                    setShowMoreInfo(value)
                  }}
                  renderHeader={() => (
                    <View style={{
                      justifyContent: 'space-between',
                      alignItems: 'center',
                      width: width * 0.80,
                      flexDirection: 'row',
                      flex:1,
                    }}>
                      <Text style={{
                        fontFamily: 'Poppins-SemiBold',
                        color: 'black',
                        flex:1
                      }}>{dataarr.questions}
                      </Text>
                      <Image style={{  flex:0}}
                        source={!showMoreInfo ? constants.images.FAQdownarrow : constants.images.NextArrow}
                      />
                    </View>
                  )}
                  renderContent={() => (
                    <View style={{ backgroundColor: constants.colors.formBackground, width: width * 0.80, }}>
                      <View style={{ backgroundColor: 'white', marginTop: 5, padding: 10, borderRadius: 10 }}>
                        <Text style={{
                          fontFamily: 'Poppins-Regular',
                          color: 'grey',
                        }}>{dataarr.answers}</Text>
                      </View>
                    </View>
                  )}
                />
              </View>
            );
          })}
        </ScrollView>
      </View>

    </View>
  );
};

const mapStateToProps = (state) => ({
  userToken: state.user.userToken,
});

export default connect(mapStateToProps)(faq);
