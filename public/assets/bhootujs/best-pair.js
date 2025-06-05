
let arr1 = [
    {
        "invoiceNo": "INV1234",
        "invoiceAmount": 100.00,
        "vendorGstin": "GSTIN123",
        "isItcAvl": true
    },
    {
        "invoiceNo": "INV5678",
        "invoiceAmount": 200.50,
        "vendorGstin": "GSTIN456",
        "isItcAvl": false
    },
    {
        "invoiceNo": "INV9101",
        "invoiceAmount": 50.00,
        "vendorGstin": "GSTIN789",
        "isItcAvl": true
    },
    {
        "invoiceNo": "INV1121",
        "invoiceAmount": 300.00,
        "vendorGstin": "GSTIN234",
        "isItcAvl": true
    }
];

let arr2 = [
    {
        "invoiceNo": "INV9101",
        "invoiceAmount": 55.00,
        "vendorGstin": "GSTIN780",
        "isItcAvl": true
    },
    {
        "invoiceNo": "INV1121",
        "invoiceAmount": 300.00,
        "vendorGstin": "GSTIN234",
        "isItcAvl": true
    },
    {
        "invoiceNo": "INV9101",
        "invoiceAmount": 50.00,
        "vendorGstin": "GSTIN789",
        "isItcAvl": true
    }
];


function calculateWeight(array1, array2) {
    let arr1; let arr2; let isArrSwaped = false;
    if (array1.length > array2.length) {
        arr1 = Array.from(array1);
        arr2 = Array.from(array2);
    } else {
        arr1 = Array.from(array2);
        arr2 = Array.from(array1);
        isArrSwaped = true;
    }

    function findWeight(obj1=null, obj2=null) {
        obj1 = obj1==null ? {} : obj1;
        obj2 = obj2==null ? {} : obj2;
        let tempWeight = 0;
        Object.keys(obj1).map((objKey) => {
            if (obj1[objKey] == obj2[objKey]) {
                tempWeight++;
            }
        });
        return tempWeight;
    }
    let result = [];
    for (let i = 0; i < arr1.length; i++) {
        object1 = arr1[i]!=undefined?arr1[i]:null;
        object2 = arr2[i]!=undefined?arr2[i]:null;
        result.push({
            arr1:isArrSwaped?object2:object1,
            arr2:isArrSwaped?object1:object2,
            weight:findWeight(object1, object2)
        });
    }
    return result;
}


function findBestPair(array1, array2) {
    let arr1; let arr2; let isArrSwaped = false;
    if (array1.length > array2.length) {
        arr1 = Array.from(array1);
        arr2 = Array.from(array2);
    } else {
        arr1 = Array.from(array2);
        arr2 = Array.from(array1);
        isArrSwaped = true;
    }

    function findObjPairFromArray(object, array, weight = 4) {
        newArray = Array.from(array);
        function findWeight(obj1, obj2) {
            let tempWeight = 0;
            Object.keys(obj1).map((objKey) => {
                if (obj1[objKey] == obj2[objKey]) {
                    tempWeight++;
                }
            });
            return tempWeight;
        }
        let matchObject = null;
        for (let i = 0; i < newArray.length; i++) {
            if (findWeight(object, newArray[i]) == weight) {
                matchObject = newArray[i];
                newArray.splice(i, 1);
                break;
            }
        }
        return {
            match: matchObject,
            array: newArray
        };
    }

    let result = [];
    for (let weight = Object.keys(arr1[0]).length; weight >= 0; weight--) {
        for (let i = 0; i < arr1.length; i++) {
            object = arr1[i];
            if (arr2.length == 0 && weight == 0) {
                result.push({
                    arr1: isArrSwaped ? null : object,
                    arr2: isArrSwaped ? object : null,
                    weight: weight
                });
                arr1.splice(i, 1);
                i--;
            } else {
                let matchObj = findObjPairFromArray(object, arr2, weight);
                if (matchObj.match != null) {
                    arr2 = matchObj.array;
                    result.push({
                        arr1: isArrSwaped ? matchObj.match : object,
                        arr2: isArrSwaped ? object : matchObj.match,
                        weight: weight
                    });
                    arr1.splice(i, 1);
                    i--;
                }
            }
        }
    }
    return result;
}


// let ans = findBestPair(arr1, arr2);
// let calWeight = calculateWeight(arr1, arr2);
// console.log(ans);
// console.log(calWeight);
