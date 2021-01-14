import React, {Component} from 'react';
import {StyleSheet, Text, View, SafeAreaView, Platform} from 'react-native';
import {WebView} from 'react-native-webview';

class Webview extends Component {
  render() {
    return (
      <SafeAreaView style={{flex: 7, margin: 10, borderRadius: 10}}>
        <WebView
          scalesPageToFit={Platform.OS === 'android' ? false : true}
          source={{uri: this.props.uri}}
          startInLoadingState={true}
        />
      </SafeAreaView>
    );
  }
}
export default Webview;
