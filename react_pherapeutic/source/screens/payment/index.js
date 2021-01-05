import React, {useEffect, useState} from 'react';
import {
  View,
  Text,
  TouchableOpacity,
  Dimensions,
  Image,
  TextInput,
} from 'react-native';
import constants from '../../utils/constants';
import styles from './styles';
import SubmitButton from '../../components/submitButton';
import {KeyboardAwareScrollView} from 'react-native-keyboard-aware-scroll-view';
import AwesomeAlert from 'react-native-awesome-alerts';
import {connect} from 'react-redux';
import APICaller from '../../utils/APICaller';
import {UIActivityIndicator} from 'react-native-indicators';

const {height, width} = Dimensions.get('window');

const AddCard = (props) => {
  const [cardNumber, setCardNumber] = useState('');
  const [cardName, setCardName] = useState('');
  const [cardExpiry, setCardExpiry] = useState('');
  const [cvv, setCvv] = useState('');
  const [showAlert, setShowAlert] = useState(false);
  const [message, setMessage] = useState('');
  const [success, setSuccess] = useState(false);
  const [loading, setLoading] = useState(false);

  const {navigation, route, dispatch, userToken} = props;
  const {params} = route;

  const handlingCardNumber = (number) => {
    if (number.indexOf('.') >= 0 || number.indexOf(',') >= 0) {
      return;
    }
    setCardNumber(
      number
        .replace(/\s?/g, '')
        .replace(/(\d{4})/g, '$1 ')
        .trim(),
    );
  };

  const expiryDate = (value) => {
    if (value.indexOf('.') >= 0 || value.length > 5) {
      return;
    }

    if (value.length === 2 && cardExpiry.length === 1) {
      value += '/';
    }
    setCardExpiry(value);
    //then update state cardExpiry
  };

  const handlingCvv = (number) => {
    if (number.indexOf('.') >= 0 || number.indexOf(',') >= 0) {
      return;
    }
    setCvv(number);
  };
  const onAddCard = () => {
    if (!cardName) {
      setMessage('Please Enter Card Name.');
      setShowAlert(true);
    } else if (!cardNumber) {
      setMessage('Please Enter Card Number.');
      setShowAlert(true);
    } else if (!cardExpiry) {
      setMessage('Please Enter Expiry of your card');
      setShowAlert(true);
    } else if (!cvv) {
      setMessage('Please Enter cvv');
      setShowAlert(true);
    } else {
      setLoading(true);
      let stripe_url = 'https://api.stripe.com/v1/';
      let secret_key =
        'pk_test_51HyyqOEtbCkJm4K13uZJTnLdx473i22Mw4QjxYccE08eKN9n7i4Zw8sbU3iSTRWuL0pP6Oy3WsmyOgNA6HN6yWQC00OHQhokD7';
      setCardName('');
      setCardNumber('');
      setCardExpiry('');
      setCvv('');
      const cardDetails = {
        'card[number]': cardNumber,
        'card[exp_month]': cardExpiry.split('/')[0],
        'card[exp_year]': cardExpiry.split('/')[1],
        'card[cvc]': cvv,
      };
      var formBody = [];
      for (var property in cardDetails) {
        var encodedKey = encodeURIComponent(property);
        var encodedValue = encodeURIComponent(cardDetails[property]);
        formBody.push(encodedKey + '=' + encodedValue);
      }
      formBody = formBody.join('&');
      return fetch(stripe_url + 'tokens', {
        method: 'post',
        headers: {
          Accept: 'application/json',
          'Content-Type': 'application/x-www-form-urlencoded',
          Authorization: 'Bearer ' + secret_key,
        },
        body: formBody,
      })
        .then((response) => response.text())
        .then((result) => {
          result = JSON.parse(result);
          console.log('result api---------------', result);
          if(result.id)
          {
          const body = {
            card_token: result.id,
          };
          const endpoint = 'addUserCard';
          const method = 'POST';
          const headers = {
            'Content-Type': 'application/json',
            Authorization: `Bearer ${userToken}`,
            Accept: 'application/json',
          };
          APICaller(endpoint, method, body, headers)
            .then((response) => {
              setLoading(false);
              console.log('response Adding card => ', response['data']);
              const {status, statusCode, message, data} = response['data'];
            //  if (message === 'success') {
                setMessage(message);
               // setMessage('Your Card has been saved sucessfully.');
                setShowAlert(true);
                setSuccess(true);
             // }
            })
            .catch((error) => {
              setLoading(false);
              console.log('error Adding Card => ', error['data']);
              const {status, statusCode, message, data} = error['data'];
              setMessage(message);
              setShowAlert(true);
            });
          }
          else{
            setLoading(false);
            setMessage(result.error.message);
            setShowAlert(true);
           
          }
        });
    }
  };
  return (
    <KeyboardAwareScrollView
      showsVerticalScrollIndicator={false}
      contentContainerStyle={{width: '100%', flex: 1}}
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
            style={{flex: 1, justifyContent: 'center', alignItems: 'center'}}>
            <Image
              source={constants.images.backIcon}
              style={{height: 18, width: 10, margin: 10}}
            />
          </TouchableOpacity>
          <View
            style={{flex: 5, justifyContent: 'center', alignItems: 'center'}}>
            <Text style={styles.headingText}>Add a card</Text>
          </View>
          <View style={{flex: 1}} />
        </View>
        <View style={styles.content}>
          <View style={styles.heading}>
            <Text style={styles.topText}>
              We accept credit and debit cards from Visa, Mastercard and
              American Express.
            </Text>
          </View>
          <View style={styles.formWrap}>
            {/* <Text style={styles.fieldName}>OLD PASSWORD</Text> */}
            <View style={styles.formField}>
              <Text style={styles.fieldName}>NAME ON CARD</Text>
              <View style={styles.fieldInputWrap}>
                <TextInput
                  style={styles.fieldInput}
                  onChangeText={(text) => setCardName(text)}
                  value={cardName}
                  autoCapitalize={'none'}
                />
              </View>
            </View>
            <View style={styles.formField}>
              <Text style={styles.fieldName}>CARD NUMBER</Text>
              <View style={styles.fieldInputWrap}>
                <TextInput
                  style={styles.fieldInput}
                  maxLength={19}
                  onChangeText={(text) => handlingCardNumber(text)}
                  value={cardNumber}
                  autoCapitalize={'none'}
                  keyboardType="number-pad"
                />
              </View>
            </View>
            <View
              style={{
                flexDirection: 'row',
                marginTop: 10,
              }}>
              <View style={styles.formField1}>
                <Text style={styles.fieldName}>EXPIRY DATE(MM/YY)</Text>
                <View style={styles.fieldInputWrap1}>
                  <TextInput
                    style={styles.fieldInput1}
                    onChangeText={(text) => expiryDate(text)}
                    value={cardExpiry}
                    autoCapitalize={'none'}
                    keyboardType="number-pad"
                  />
                </View>
              </View>
              <View style={styles.formField2}>
                <Text style={styles.fieldName}>CVV</Text>
                <View style={styles.fieldInputWrap2}>
                  <TextInput
                    style={styles.fieldInput2}
                    onChangeText={(text) => handlingCvv(text)}
                    value={cvv}
                    maxLength={3}
                    autoCapitalize={'none'}
                    secureTextEntry={true}
                    keyboardType="number-pad"
                  />
                </View>
              </View>
            </View>
            <View style={styles.formField3}>
              <SubmitButton
                title={'ADD CARD'}
                submitFunction={() => onAddCard()}
              />
            </View>
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
        {loading && (
          <View
            style={{
              justifyContent: 'center',
              alignItems: 'center',
              position: 'absolute',
            }}>
            <UIActivityIndicator color={'rgb(191,54,160)'} />
          </View>
        )}
      </View>
    </KeyboardAwareScrollView>
  );
};
const mapStateToProps = (state) => ({
  userToken: state.user.userToken,
});
export default connect(mapStateToProps)(AddCard);
