import React, { Component, useState } from 'react';

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
import { connect } from 'react-redux';
import { saveUser } from '../../redux/actions/user';
import AwesomeAlert from 'react-native-awesome-alerts';
import { validateEmail } from '../../utils/validateStrings';
import Header from '../../components/Header';
import Webview from '../../components/webView';
const { height, width } = Dimensions.get('window');


class StripeConnect extends Component {
  constructor(props) {
    super(props);
  //  console.log(this.props.route.params)
    const { client_id, stripe_connect_url } = this.props.route.params;

    this.state = { client_id: client_id, stripe_connect_url: stripe_connect_url, showAlert: false, message: '', success: false }
  }


  setCurrentUrl = (newNavState) => {
    console.log(newNavState)
    if (newNavState.url.includes('&code=')) {

      // console.log(newNavState.url.split('&code=')[1])
      //alert(newNavState.url.split('&code=')[1])
      // const endpoint='connectWithStripe';
      // const method = 'POST';
      // const headers = {
      //   'Content-Type': 'application/json',
      //   Authorization: `Bearer ${this.props.userToken}`,
      //   Accept: 'application/json',
      // };
      // console.log(headers)
      //  let body = new FormData();
      // body.append('code', newNavState.url.split('&code=')[1]);
      //  body.append('code','ac_J4no6bHGQ5nean7h48hzvWT3CQs0m15Q');

      // const loginObj = {
      //   code: 'ac_J4no6bHGQ5nean7h48hzvWT3CQs0m15Q',
      // };

      // alert(this.props.userToken)
      // console.log('body',JSON.stringify(body))
      // APICaller(endpoint, method, loginObj, headers)
      //   .then((response) => {
      //     console.log(response)
      //   })
      //   .catch((error) => {
      //    console.log('error calling therapist => ', error);
      //   });


      // let finalObj = {code:newNavState.url.split('&code=')[1]}
      // console.log("finalobj", finalObj);

      const headers = {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${this.props.userToken}`,
        'Accept': 'application/json',
      };
      APICaller('connectWithStripe?code=' + newNavState.url.split('&code=')[1] + '', 'GET', null, headers)
        .then(response => {
          console.log('response logging in => ', response['data']);
          const { data, message, status, statusCode } = response['data'];
          if (status === 'success') {

            this.setState({ success: true })
            this.setState({ message: message })
            this.setState({ showAlert: true })
            //  navigation.navigate('Home');
          }


        })
        .catch(error => {
          console.log('error logging in => ', error);
          // navigation.navigate('Home');
          // const { data, message, status, statusCode } = error['data'];


        })

    }
    // alert(newNavState)
  };




  render() {

    //return <WebView source={{ uri: 'https://reactnative.dev/' }} />;
    return (
      <>
        <Image
          source={constants.images.background}
          resizeMode={'stretch'}
          style={styles.containerBackground}
        />
        <Header title="Manage Payment" navigation={this.props.navigation} />
        <Webview
          parentMethod={this.setCurrentUrl}
          // onRef={ref => (this.Webview = ref)}
          // renderLoading={() => (
          //   <ActivityIndicator
          //     color='black'
          //     size='large'
          //     style={styles.flexContainer}
          //   />
          // )}
          // uri={'https://connect.stripe.com/oauth/authorize?response_type=code&client_id='+this.state.client_id+'&scope=read_write&redirect_uri=https://smarttask.yapits.com/stripe/connect/oath/callback'}
          uri={this.state.stripe_connect_url}
        // onNavigationStateChange={this.handleWebViewNavigationStateChange}
        // onNavigationStateChange={navState => {
        //   this.setCurrentUrl(navState)
        // }}

        />
        {/* <Webview
          ref={(ref) => (this.webview = ref)}
          source={{ uri: 'https://reactnative.dev/' }}
          onNavigationStateChange={this.handleWebViewNavigationStateChange}
        /> */}
        {/* <Webview
          source={{ uri: 'https://connect.stripe.com/oauth/authorize?response_type=code&client_id=ca_IH2U8V2KfbTC5dmZHMT0CzaYFbm4Ydsm&scope=read_write&redirect_uri=https://smarttask.yapits.com/stripe/connect/oath/callback' }}
          startInLoadingState={true}
          renderLoading={() => (
            <ActivityIndicator
              color='black'
              size='large'
              style={styles.flexContainer}
            />
          )}
          //ref={webviewRef}
          onNavigationStateChange={navState => {
            // setCanGoBack(navState.canGoBack)
            // setCanGoForward(navState.canGoForward)
            this.setCurrentUrl(navState.url)
          }}
        /> */}
        <AwesomeAlert
          show={this.state.showAlert}
          showProgress={false}
          message={this.state.message}
          closeOnTouchOutside={true}
          showConfirmButton={true}
          confirmText="Confirm"
          confirmButtonColor={constants.colors.lightGreen}
          contentContainerStyle={{
            minWidth: 200,
            // padding: utils.wp(10),
            // borderRadius: utils.wp(10),
            // backgroundColor: this.props.backgroundColor
          }}
          onCancelPressed={() => {
            this.setState({ showAlert: false })
            if (this.state.success) this.props.navigation.goBack();
          }}
          onConfirmPressed={() => {
            this.setState({ showAlert: false })
            if (this.state.success) this.props.navigation.goBack();
          }}
          onDismiss={() => {
            this.setState({ showAlert: false })
            // if (success) navigation.goBack();
          }}
        />
      </>

    );

  }

}
const mapStateToProps = (state) => ({
  userToken: state.user.userToken,
  // userData: state.user.userData,
});

export default connect(mapStateToProps)(StripeConnect);
///export default StripeConnect;
