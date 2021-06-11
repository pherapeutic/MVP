import React, {Component} from 'react';
import {StyleSheet, Text, View, SafeAreaView, Platform,ActivityIndicator} from 'react-native';
import {WebView} from 'react-native-webview';

class Webview extends Component {
  constructor(props) {
    super(props);
    };
  //   click = (url) => {
  //     alert(url)
  //    // this.props.parentMethod(url);
  // }
  render() {
    console.log('fdfd',this.props)
    return (
      <SafeAreaView style={{flex: 7, margin: 10, borderRadius: 10}}>
        <WebView
          renderLoading={() => (
            <ActivityIndicator
              color='black'
              size='large'
              
              // style={{ flex: 1,
              //   justifyContent: 'center',
              //   alignItems: 'center',
              //   }}
            />
           )}
          scalesPageToFit={Platform.OS === 'android' ? false : true}
          source={{uri: this.props.uri}}
          startInLoadingState={true}
          onNavigationStateChange={navState => {
            this.props.parentMethod(navState)
          }}
        />
      </SafeAreaView>
    );
  }
}
export default Webview;
