import React, {useState, useEffect} from 'react';
import {
  View,
  Text,
  TouchableOpacity,
  StyleSheet,
  Dimensions,
  Image,
  TextInput,
  FlatList,
} from 'react-native';
import constants from '../../utils/constants';
import styles from './styles';
import SubmitButton from '../../components/submitButton';
import Header from '../../components/Header';
import APICaller from '../../utils/APICaller';
import {connect} from 'react-redux';
import AwesomeAlert from 'react-native-awesome-alerts';
import {defaultAlert} from '../../components/DefaultAlert';
// import { FlatList } from 'react-native-gesture-handler';
import {UIActivityIndicator} from 'react-native-indicators';
import LinearGradient from 'react-native-linear-gradient';
import Events from '../../utils/events';
import {useFocusEffect} from '@react-navigation/native';
const {height, width} = Dimensions.get('window');

const selectPaymentMethod = (props) => {
  const [cardDetails, setCardDetails] = useState(null);
  const [therapistId, setTherapistId] = useState(null);
  const [therapistDetail, setTherapistDetail] = useState(null);

  const [selectedCardIndex, setSelectedCardIndex] = useState(-1);
  const [selectedCardDetails, setSelectedCardDetails] = useState(null);
  const [showAlert, setShowAlert] = useState(false);
  const [showAlert_payment, setshowAlert_payment] = useState(false);
  const [message, setMessage] = useState('');
  
  const [alertMessage, setAlert] = useState('');
  const [loading, setLoading] = useState(false);
  const [showDeleteModal, setShowDeleteModal] = useState(false);
  const [secret_key, setSecret_key] = useState('');
  const {navigation, dispatch, route, userToken} = props;

  // const {navigation, route, dispatch} = props;
  const {params} = route;
  useEffect(() => {
    if (params && params.therapistId) {
      // alert(params.therapistId);

      setTherapistId(params.therapistId);
      setTherapistDetail(params.therapist);
    }
    getCardDetails();
    getstripeData();
  }, []);
  useFocusEffect(
    React.useCallback(() => {
      
      getCardDetails();
    }, []),
  );
  const  getstripeData = () => {
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
        console.log(data)
       //  this.setState('client_id',data.client_id)
       setSecret_key(data.secret_id)
        }
      })
      .catch((error) => {
      //  console.log('error getting user profile => ', error);
      });
  };
  const getCardDetails = () => {
    setLoading(true);
    const endpoint = 'getUserCards';
    const method = 'GET';
    const headers = {
      'Content-Type': 'application/json',
      Authorization: `Bearer ${userToken}`,
      Accept: 'application/json',
    };
    APICaller(endpoint, method, null, headers)
      .then((response) => {
        setLoading(false);
        const {status, statusCode, message, data} = response['data'];

        console.log('response getting user card details => ', response['data']);
        setCardDetails(data);

        if (data && data.length > 0) {
          setSelectedCardIndex(0);
          setSelectedCardDetails(data[0]);
        }

        if (message === 'success') {
          // dispatch(saveUserProfile(data));
          console.log('success=========');
        }
      })
      .catch((error) => {
        setLoading(false);
        console.log('error getting user card details => ', error);
      });
  };
  const deleteCard = (index, cardId) => {
    setLoading(true);
    var array = [...cardDetails];
    const endpoint = `deleteUserCard?card_id=${cardId}`;
    const method = 'DELETE';
    const headers = {
      'Content-Type': 'application/json',
      Authorization: `Bearer ${userToken}`,
      Accept: 'application/json',
    };
    APICaller(endpoint, method, null, headers)
      .then((response) => {
        setLoading(false);
        console.log('response deleting card=====> ', response['data']);
        const {status, statusCode, message, data} = response['data'];
        if (message === 'success') {
          if (index !== -1) {
            array.splice(index, 1);
            setCardDetails(array);
          }
          setAlert('Card deleted Successfully');
          setShowAlert(true);
        }
      })
      .catch((error) => {
        setLoading(false);
        console.log('error deleting card => ', error['data']);
      });
  };

  const createCall = () => {
    // alert('Called');
    // alert(JSON.stringify(selectedCardDetails));
    if (!selectedCardDetails) {
      setAlert('Please Select Any Payment Method');
      setShowAlert(true);
    } else {
      // alert(JSON.stringify(selectedCardDetails));
      Events.trigger('showModalLoader');
      const endpoint = 'createCall';
      const method = 'POST';
      const headers = {
        'Content-Type': 'application/json',
        Authorization: `Bearer ${userToken}`,
        Accept: 'application/json',
      };
      var body = {
        card_id: selectedCardDetails.id,
        therapist_id: therapistId,
      };
      console.log('data before sending => ', body);
      // alert(JSON.stringify(body));
      APICaller(endpoint, method, body, headers)
        .then((response) => {
          Events.trigger('hideModalLoader');
          console.log('response after creating Call => ', response['data']);
          const {status, statusCode, message, data} = response['data'];
          if (statusCode === 200) {
            callTherapist(data);
          } else {
            setAlert(message);
            setShowAlert(true);
          }
          // alert(JSON.stringify(response));
        })
        .catch((error) => {
          // alert(JSON.stringify(error));
          Events.trigger('hideModalLoader');
          console.log('error updating user profile => ', error['data']);
          const {status, statusCode, message, data} = error['data'];
          setAlert(message);
          setShowAlert(true);
        });
    }
  };
  const callTherapist = (data) => {
    // alert(JSON.stringify(therapistDetail));
    console.log('therapist details => ', therapistDetail);

    let channelname = 'channel_' + Date.now();
    const endpoint =
      'sendVideoCallNotificationToTherapist/' +
      therapistDetail.user_id +
      '?channel_name=' +
      channelname;
    const method = 'GET';
    const headers = {
      'Content-Type': 'application/json',
      Authorization: `Bearer ${userToken}`,
      Accept: 'application/json',
    };
    APICaller(endpoint, method, null, headers)
      .then((response) => {
        //   console.log('userToken'+userToken)
        console.log('response calling push => ', response);
        navigation.navigate('VideoCall', {
          CallReciverName:therapistDetail.first_name,
          channelnamedata: channelname,
          callDetails: data,
        });
      })
      .catch((error) => {
        console.log('error calling therapist => ', error);
      });
  };
  
  const showpaymennt = () => {
    if (!selectedCardDetails) {
      setAlert('Please Select Any Payment Method');
      setShowAlert(true);
    }
    else{
      setshowAlert_payment(true)
     }
  }
  const createDefaultCard = (index, cardId) => {
    console.log('card');
    setLoading(true);
    const endpoint = 'createDefaultCard';
    const method = 'POST';
    const headers = {
      'Content-Type': 'application/json',
      Authorization: `Bearer ${userToken}`,
      Accept: 'application/json',
    };
    const body = {
      card_id: cardId,
    };
    APICaller(endpoint, method, body, headers)
      .then((response) => {
        setLoading(false);
        const {status, statusCode, message, data} = response['data'];

        console.log('response getting user card details => ', response['data']);
        // alert(JSON.stringify(response['data']));
        // setCardDetails(data);
        setSelectedCardIndex(index);
        console.log('cardDetails'+JSON.stringify(cardDetails[index]))
        setSelectedCardDetails(cardDetails[index]);

        if (message === 'success') {
          // dispatch(saveUserProfile(data));
          console.log('success=========');
        }
      })
      .catch((error) => {
        setLoading(false);
        console.log('error getting user card details => ', error);
      });
  };
  
  return (
    <View style={styles.container}>
      <Image
        source={constants.images.background}
        resizeMode={'stretch'}
        style={styles.containerBackground}
      />

      <Header title="Select payment method" navigation={navigation} />
      <View
        style={{flex: 7, justifyContent: 'flex-start', alignItems: 'center'}}>
        <FlatList
          data={cardDetails}
          showsVerticalScrollIndicator={false}
          ListEmptyComponent={
            <Text style={{color: constants.colors.white}}>
              No Payment Methods Found
            </Text>
          }
          renderItem={({item, index}) => {
            return (
              <>
                <TouchableOpacity
                  style={{
                    marginTop: 20,
                  
              
                  }}
                  onPress={() => {
                    createDefaultCard(index, item.id);
                  }}>
                  <LinearGradient
                    start={{x: 0, y: 1}}
                    end={{x: 1, y: 1}}
                    colors={
                      index == selectedCardIndex
                        ? ['#228994', '#3BD8E5']
                        : [constants.colors.white, constants.colors.white]
                    }
                    style={{
                      backgroundColor: constants.colors.white,
                      width: '95%',
                      flexDirection: 'row',
                      justifyContent: 'space-around',
                      alignSelf: 'center',
                      borderRadius:5
                      // marginVertical: Dimensions.get('window').height * 0.005
                    }}>
                    <Image source={constants.images.visa} />
                    <View
                      style={{
                        flexDirection: 'column',
                        justifyContent: 'center',
                        width:'70%'
                      }}>
                      <Text
                        style={{
                          fontSize: 14,
                         // textAlign:'left',
                          color:
                            index == selectedCardIndex
                              ? constants.colors.white
                              : constants.colors.black,
                          fontWeight: '500',
                        }}>
                        **** **** **** {item.last4}
                      </Text>
                      <Text
                        style={{
                          fontSize: 14,
                          color:
                            index == selectedCardIndex
                              ? constants.colors.white
                              : constants.colors.black,
                          fontWeight: '500',
                          bottom: 3,
                        }}>
                        Expires {item.exp_month}/{item.exp_year}
                      </Text>
                    </View>
                    {/* <SubmitButton
                      title={'Delete'}
                      size={'Small'}
                      textColor={
                        index == selectedCardIndex
                          ? constants.colors.darkGreen
                          : constants.colors.white
                      }
                      customcolors={
                        index == selectedCardIndex ? ['#fff', '#fff'] : null
                      }
                      submitFunction={() => {
                        defaultAlert('Are you sure want to delete card?', [
                          {text: 'Cancel'},
                          {
                            text: 'Confirm',
                            onPress: () => deleteCard(index, item.id),
                          },
                        ]);
                      }}
                    /> */}
                    <View>

                    </View>
                  </LinearGradient>
                </TouchableOpacity>
                {/* <TouchableOpacity
                  onPress={() => {
                    defaultAlert(
                      'Are you sure want to set this default card?',
                      [
                        {text: 'Cancel'},
                        {
                          text: 'Confirm',
                          onPress: () => createDefaultCard(index, item.id),
                        },
                      ],
                    );
                  }}>
                  <Text
                    style={{
                      color: 'white',
                      margin: 10,
                      fontSize: 15,
                    }}>
                    Set as default
                  </Text>
                </TouchableOpacity> */}
              </>
            );
          }}
        />
      </View>
      <View style={{position: 'absolute', bottom: 30}}>
      <SubmitButton
          title={'ADD NEW CARD'}
          customcolors={['#808080', '#808080']}
          submitFunction={() => navigation.navigate('AddCard',{secret_key_data:secret_key})}
        />
        <View style={{height: 20}} />
        <SubmitButton
          title={'CONTINUE'}
          //submitFunction={() => createCall()}
          submitFunction={() => showpaymennt()}
          // submitFunction={() => (!selectedCardDetails&&
          //     setAlert('Please Select Any Payment Method');
          //   setShowAlert(true);
          //   :
          //   setshowAlert_payment(true);)
          // }

          //submitFunction={() => navigation.goBack()}
    
        />
       

        {/* <TouchableOpacity
          style={{
            justifyContent: 'center',
            alignItems: 'center',
            backgroundColor: '#333333',
            marginTop: 10,
            borderRadius: 5,
          }}
          onPress={() => navigation.goBack()}>
          <Text
            style={{
              color: constants.colors.white,
              fontWeight: '500',
              fontSize: 16,
              padding: 7,
            }}>
            DO THIS LATER
          </Text>
        </TouchableOpacity> */}
      </View>
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
          <AwesomeAlert
        show={showAlert_payment}
        showProgress={false}
        message={'Payment will be deducted  from your card for this call.'}
        closeOnTouchOutside={true}
        showConfirmButton={true}
        showCancelButton={true}
        confirmText="Continue"
        confirmButtonColor={constants.colors.lightGreen}
        onCancelPressed={() => {
          setshowAlert_payment(false);
        }}
        onConfirmPressed={() => {
          createCall()
          setshowAlert_payment(false);
        }}
        onDismiss={() => {
          setshowAlert_payment(false);
        }}
      />
      {loading && (
        <View
          style={{
            justifyContent: 'center',
            alignItems: 'center',
            position: 'absolute',
            backgroundColor:'white',
            padding:10
          }}>
          <UIActivityIndicator color={'rgb(191,54,160)'} />
        </View>
      )}
    </View>
  );
};
const mapStateToProps = (state) => ({
  userToken: state.user.userToken,
});
export default connect(mapStateToProps)(selectPaymentMethod);
