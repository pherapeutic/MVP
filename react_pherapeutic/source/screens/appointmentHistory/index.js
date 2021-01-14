import React, { useEffect, useState } from 'react';
import {
    View,
    Text,
    TouchableOpacity,
    StyleSheet,
    Dimensions,
    Image,
    TextInput,
    Alert,
    FlatList,
    Switch,
} from 'react-native';
import Moment from 'moment';
import constants from '../../utils/constants';
import styles from './styles';
import APICaller from '../../utils/APICaller';
import { AirbnbRating } from 'react-native-ratings';

import { connect } from 'react-redux';
import Header from '../../components/Header';
import AppointmentHistoryModel from '../appointmentHistoryModel';

const { height, width } = Dimensions.get('window');
const AppointmentHistory = (props) => {
    const [data, setData] = useState([]);
    const [showModal, setShowModal] = useState(false);

    const { navigation, userData, dispatch, userToken } = props;

    useEffect(() => {
        getList();
    }, []);
    const getList = () => {
        let headers = {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${userToken}`,
            Accept: 'application/json',
        };
        APICaller('getClientAppointment', 'GET', null, headers)
            .then((response) => {
                setData(response['data']['data'])

            })
            .catch((error) => {
                console.log('error=> ', error);
            });

    }
    const momentfunc = (date) => {
        Moment.locale('en');
        var dt = date;
        return Moment(dt).format("LLL");
    }

    const showObj = data.map(obj => obj);
    const imageShow = showObj.image? showObj.image: constants.images.defaultUserImage ;
    return (
        <View style={styles.container}>
            <Image
                resizeMode={'stretch'}
                source={constants.images.background}
                style={styles.containerBackground}
            />

            <Header title="Appointments History" navigation={navigation} />
            <View style={styles.content}>

                <FlatList
                    data={data}
                    style={{ backgroundColor: 'rgba(52, 52, 52, 0)' }}
                    keyExtractor={(item, index) => {
                        return index.toString();
                    }}
                    renderItem={({ item }) => (
                        item == null ?
                            null
                            : <View style={styles.cardView}>

                                <View style={styles.imageView}>
                                    <Image
                                        style={styles.imageSize}
                                        source={
                                            item.image ? {uri: item.image} : constants.images.defaultUserImage
                                          }
                                    />
                                </View>
                                <View style={styles.contentView}>
                                    <View style={{ flexDirection: "row" }}>
                                        <Text style={{ fontSize: 14, color: "rgb(20,20,20)", fontWeight: "bold" }}>{item.first_name} {item.last_name}</Text>
                                    </View>
                                    <View style={{ flexDirection: "row" }}>
                                        <Text style={{ fontSize: 10, color: "rgb(4,4,4)" }}>{momentfunc(item.created_at)}</Text>
                                    </View>
                                    <View style={{ flexDirection: "row", height: 20, width: 110, borderRadius: 4, backgroundColor: "rgb(1,216,251)", marginTop: 5 }}>
                                        <Text style={{ paddingLeft: 5 ,fontSize:10,paddingTop:3}}>{item.rating}</Text>
                                        <View style={{paddingLeft:5,justifyContent:"center"}}>
                                        <AirbnbRating
                                            showRating={false}
                                            isDisabled={true}
                                            count={5}
                                            size={10}
                                            defaultRating={item.rating}
                                            selectedColor="white"
                                            ratingColor='#3498db'
                                            ratingBackgroundColor="rgb(24,173,189)"
                                            starContainerStyle="white"


                                        />
                                        </View>
                                    </View>
                                </View>
                                <View style={styles.paidView}>
                                    <View style={{ flexDirection: "row", justifyContent: "flex-end" }}><Text style={{ color: "rgb(20,20,20)", fontSize: 14, fontWeight: "bold" }}>Paid:{item.amount}</Text></View>
                                    <View style={{ flexDirection: "row", justifyContent: "flex-end",paddingTop:5 }}><Text style={{ color: "rgb(4,4,4)", fontSize: 9, textDecorationLine: 'underline' }}
                                        onPress={() => {
                                            setShowModal((prevState) => !prevState);
                                        }} >VIEW NOTES</Text>

                                    </View>
                                </View>

                            </View>

                    )}
                />
            </View>
            <AppointmentHistoryModel
                data={data}
                showModal={showModal}
                setShowModal={setShowModal}
                navigation={navigation}
            />
        </View>


    );
};

const mapStateToProps = (state) => ({
    userData: state.user.userData,
    userToken: state.user.userToken,
});

export default connect(mapStateToProps)(AppointmentHistory);
